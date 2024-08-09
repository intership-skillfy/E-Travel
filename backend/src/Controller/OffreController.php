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
use App\Service\FileUploader;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

#[Route('/api/offres')]
class OffreController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(private FileUploader $fileUploader,EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    #[Route('/new', name: 'api_offre_new', methods: ['POST'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'detailedDescription', type: 'string'),
                new OA\Property(property: 'images', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'startDate', type: 'string'),
                new OA\Property(property: 'endDate', type: 'string'),
                new OA\Property(property: 'destination', type: 'string'),            ]
        )
    )]
    public function new(Request $request, FileUploader $fileUploader): JsonResponse
    {
        $type = $request->query->get('type');
 
        // Check if the request is multipart/form-data
        if ($request->isMethod('POST') && $request->getContentType() === 'form') {
            $data = $request->request->all(); // Retrieves form data
            $files = $request->files->all(); // Retrieves uploaded files
            // dd($request->files->all());
            // Validate required fields
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
 
            // Handle banner upload
            if ($request->files->has('banner')) {
                $bannerFile = $request->files->get('banner');
                if ($bannerFile instanceof UploadedFile) {
                    $bannerFileName = $fileUploader->upload($bannerFile);
                    $offre->setBanner($bannerFileName);
                }
            }
 
          // Handle images upload
        if ($request->files->has('images')) {
            $imagesFiles = $request->files->get('images');
            dd($imagesFiles);
            if (is_array($imagesFiles)) {
                $uploadedImages = [];
                foreach ($imagesFiles as $imageFile) {
                    if ($imageFile instanceof UploadedFile) {
                        $uploadedImages[] = $fileUploader->upload($imageFile);
                    }
                }
                $offre->setImages($uploadedImages);
            }
        }
 
            $offre->setStartDate(new \DateTimeImmutable($data['startDate']));
            $offre->setEndDate(new \DateTimeImmutable($data['endDate']));
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
        } else {
            return new JsonResponse('Invalid request type or content type', JsonResponse::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/{id}', name: 'api_offre_show', methods: ['GET'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the offer to retrieve',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns a single offer',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
    public function show(Offre $offre): JsonResponse
    {
        $data = $this->serializer->serialize($offre, 'json', [
            AbstractNormalizer::GROUPS => ['offre:read']
        ]);

        return new JsonResponse($data, 200, [], true);
    }



    #[Route('/{id}/edit', name: 'api_offre_edit', methods: ['PUT'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the offer to update',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'detailedDescription', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'banner', type: 'string'),
                new OA\Property(property: 'images', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'startDate', type: 'string'),
                new OA\Property(property: 'endDate', type: 'string'),
                new OA\Property(property: 'destination', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Updates information of an offer',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
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
    #[OA\Tag(name: 'Offer')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of offers',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Excursion::class))
        )
    )]
    public function index(OffreRepository $offreRepository): JsonResponse
    {
        $offres = $offreRepository->findAll();
        $data = $this->serializer->serialize($offres, 'json', [
            AbstractNormalizer::GROUPS => ['offre:read']
        ]);
        return new JsonResponse($data, 200, [], true);
    }
    
    
                

    #[Route('/{id}/delete', name: 'api_offre_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the offer to delete',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Deletes an offer'
    )]
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
