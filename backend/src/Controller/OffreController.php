<?php

namespace App\Controller;

use App\Entity\Agency;
use App\Entity\Category;
use App\Entity\Destination;
use App\Entity\Excursion;
use App\Entity\Hiking;
use App\Entity\Omra;
use App\Entity\Trip;
use App\Entity\Offre;
use App\Repository\OffreRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/offres')]
class OffreController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private SerializerService $serializerService;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator, SerializerService $serializerService)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->serializerService = $serializerService;
    }

    #[Route('/new', name: 'api_offre_new', methods: ['POST'])]
    public function new(Request $request): JsonResponse
    {
        $type = $request->query->get('type');
        $data = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse('Invalid JSON provided', JsonResponse::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['name', 'agency_id', 'categories', 'destination'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return new JsonResponse("The $field field is required", JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $offre = $this->createOffreByType($type);

        $offre->setName($data['name'] ?? '');
        $offre->setDescription($data['description'] ?? null);
        $offre->setDetailedDescription($data['detailedDescription'] ?? null);
        $offre->setImages($data['images'] ?? []);
        $offre->setStartDate(new \DateTimeImmutable($data['startDate']));
        $offre->setEndDate(new \DateTimeImmutable($data['endDate']));
        $offre->setBanner($data['banner'] ?? null);
        $offre->setIncluded($data['included'] ?? null);
        $offre->setNoIncluded($data['noIncluded'] ?? null);

        $agency = $this->entityManager->getRepository(Agency::class)->find($data['agency_id']);
        if (!$agency) {
            return new JsonResponse('Invalid agency ID', JsonResponse::HTTP_BAD_REQUEST);
        }
        $offre->setAgency($agency);

        if (isset($data['categories'])) {
            $categories = $this->entityManager->getRepository(Category::class)->findBy(['id' => $data['categories']]);
            foreach ($categories as $category) {
                $offre->addCategory($category);
            }
        }

        $destination = $this->entityManager->getRepository(Destination::class)->find($data['destination']);
        if (!$destination) {
            return new JsonResponse('Invalid destination ID', JsonResponse::HTTP_BAD_REQUEST);
        }
        $offre->setDestination($destination);

        // Set additional fields based on type
        $this->setAdditionalFields($offre, $data);

        $this->entityManager->persist($offre);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Offre created successfully!']);
    }

    #[Route('/{id}', name: 'api_offre_show', methods: ['GET'])]
    public function show(Offre $offre): JsonResponse
    {
        $data = $this->serializer->serialize($offre, 'json', [
            AbstractNormalizer::GROUPS => ['offre:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }



    #[Route('/{id}/edit', name: 'api_offre_edit', methods: ['PUT'])]
    public function edit(Request $request, Offre $offre): JsonResponse
    {
        $type = $this->getTypeByOffre($offre);
        $data = $request->getContent();

        $updatedOffre = $this->serializer->deserialize($data, get_class($offre), 'json', ['object_to_populate' => $offre]);

        $errors = $this->validator->validate($updatedOffre);
        if (count($errors) > 0) {
            return new JsonResponse((string) $errors, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        $responseData = $this->serializer->serialize($updatedOffre, 'json', [
            AbstractNormalizer::GROUPS => ['offre:read']
        ]);

        return new JsonResponse($responseData);
    }

    #[Route('/', name: 'api_offre_index', methods: ['GET'])]
    public function index(OffreRepository $offreRepository): JsonResponse
    {
        $offres = $offreRepository->findAll();
        $data = $this->serializer->serialize($offres, 'json', [
            AbstractNormalizer::GROUPS => ['offre:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'api_offre_delete', methods: ['DELETE'])]
    public function delete(Offre $offre): JsonResponse
    {
        $this->entityManager->remove($offre);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'offre deleted successfully']);
    }

    private function createOffreByType(string $type): Offre
    {
        return match ($type) {
            'excursion' => new Excursion(),
            'hiking' => new Hiking(),
            'omra' => new Omra(),
            'trip' => new Trip(),
            default => throw new \InvalidArgumentException('Invalid type'),
        };
    }

    private function getTypeByOffre(Offre $offre): string
    {
        return match (get_class($offre)) {
            Excursion::class => 'excursion',
            Hiking::class => 'hiking',
            Omra::class => 'omra',
            Trip::class => 'trip',
            default => throw new \InvalidArgumentException('Invalid offre type'),
        };
    }

    private function setAdditionalFields(Offre $offre, array $data): void
    {
        if ($offre instanceof Excursion) {
            $offre->setExtra($data['extra'] ?? false);
        }

        if ($offre instanceof Hiking) {
            $offre->setDifficulty($data['difficulty'] ?? '');
        }
    }
}
