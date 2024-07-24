<?php

namespace App\Controller;

use App\Entity\Omra;
use App\Repository\OmraRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/omra', name: 'app_omra_')]
class OmraController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[OA\Tag(name: 'Omra')]
    public function index(OmraRepository $omraRepository, SerializerService $serializerService, EntityManagerInterface $entityManager): JsonResponse
    {
        $omras = $entityManager->getRepository(Omra::class)->findAll();
        $omrasArray = $serializerService->serializeArray($omras);

        return new JsonResponse(['success' => true, 'omras' => $omrasArray]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Tag(name: 'Omra')]
    public function show(int $id, SerializerService $serializerService, EntityManagerInterface $entityManager): JsonResponse
    {
        $omra = $entityManager->getRepository(Omra::class)->find($id);
        if (!$omra) {
            return new JsonResponse(['success' => false, 'message' => 'Omra not found'], 404);
        }
        $omraArray = $serializerService->serialize($omra);

        return new JsonResponse(['success' => true, 'omra' => $omraArray]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[OA\Tag(name: 'Omra')]
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
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $omra = new Omra();
        if (isset($data['name'])) $omra->setName($data['name']);
        if (isset($data['description'])) $omra->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $omra->setDetailedDescription($data['detailedDescription']);
        if (isset($data['images'])) $omra->setImages($data['images']);
        if (isset($data['startDate'])) $omra->setStartDate(new \DateTimeImmutable($data['startDate']));
        if (isset($data['endDate'])) $omra->setEndDate(new \DateTimeImmutable($data['endDate']));
        if (isset($data['destination'])) $omra->setDestination($data['destination']);        
        $entityManager->persist($omra);
        $entityManager->flush();

        $omraArray = $serializerService->serialize($omra);

        return new JsonResponse(['success' => true, 'omra' => $omraArray], 201);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Omra')]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $omra = $entityManager->getRepository(Omra::class)->find($id);
        if (!$omra) {
            return new JsonResponse(['success' => false, 'message' => 'Omra not found'], 404);
        }
        $entityManager->remove($omra);
        $entityManager->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/{id}/edit-general', name: 'edit_general', methods: ['PUT'])]
    #[OA\Tag(name: 'Omra')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(type: 'object', 
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'detailedDescription', type: 'string'),
            ]))
    ]
    public function editGeneral(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $omra = $entityManager->getRepository(Omra::class)->find($id);
        if (!$omra) {
            return new JsonResponse(['success' => false, 'message' => 'Omra not found'], 404);
        }

        if (isset($data['name'])) $omra->setName($data['name']);
        if (isset($data['description'])) $omra->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $omra->setDetailedDescription($data['detailedDescription']);

        $entityManager->flush();

        $omraArray = $serializerService->serialize($omra);

        return new JsonResponse(['success' => true, 'omra' => $omraArray]);
    }

    #[Route('/{id}/edit-tarifs', name: 'edit_tarifs', methods: ['PUT'])]
    #[OA\Tag(name: 'Omra')]
    public function editTarifs(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $omra = $entityManager->getRepository(Omra::class)->find($id);
        if (!$omra) {
            return new JsonResponse(['success' => false, 'message' => 'Omra not found'], 404);
        }

        if (isset($data['tarifs'])) {
            // Assuming tarifs are handled separately and not part of this example
        }

        $entityManager->flush();

        $omraArray = $serializerService->serialize($omra);

        return new JsonResponse(['success' => true, 'omra' => $omraArray]);
    }

    #[Route('/{id}/edit-images', name: 'edit_images', methods: ['PUT'])]
    #[OA\Tag(name: 'Omra')]
    public function editImages(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $omra = $entityManager->getRepository(Omra::class)->find($id);
        if (!$omra) {
            return new JsonResponse(['success' => false, 'message' => 'Omra not found'], 404);
        }

        if (isset($data['images'])) $omra->setImages($data['images']);

        $entityManager->flush();

        $omraArray = $serializerService->serialize($omra);

        return new JsonResponse(['success' => true, 'omra' => $omraArray]);
    }
}
