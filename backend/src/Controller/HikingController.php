<?php

namespace App\Controller;

use App\Entity\Hiking;
use App\Repository\HikingRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
#[Route('/api/hiking'), name('app_hiking_')]
class HikingController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[OA\Tag(name: 'Hikings')]
    public function index(HikingRepository $hikingRepository, SerializerService $serializerService): JsonResponse
    {
        $hikings = $hikingRepository->findAll();
        $hikingsArray = $serializerService->serializeArray($hikings);

        return new JsonResponse(['success' => true, 'hikings' => $hikingsArray]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Tag(name: 'Hikings')]
    public function show(id $id, SerializerService $serializerService, EntityManagerInterface $entityManager): JsonResponse
    {
        $hiking = $entityManager->getRepository(Hiking::class)->find($id);
        $hikingArray = $serializerService->serialize($hiking);

        return new JsonResponse(['success' => true, 'hiking' => $hikingArray]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[OA\Tag(name: 'Hikings')]
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
                new OA\Property(property: 'destination', type: 'string'),
                new OA\Property(property: 'difficulty', type: 'string'),
            ]
        )
    )]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $hiking = new Hiking();
        if (isset($data['name'])) $hiking->setName($data['name']);
        if (isset($data['description'])) $hiking->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $hiking->setDetailedDescription($data['detailedDescription']);
        if (isset($data['images'])) $hiking->setImages($data['images']);
        if (isset($data['startDate'])) $hiking->setStartDate(new \DateTimeImmutable($data['startDate']));
        if (isset($data['endDate'])) $hiking->setEndDate(new \DateTimeImmutable($data['endDate']));
        if (isset($data['destination'])) $hiking->setDestination($data['destination']);
        if (isset($data['difficulty'])) $hiking->setDifficulty($data['difficulty']);
        
        $entityManager->persist($hiking);
        $entityManager->flush();

        $hikingArray = $serializerService->serialize($hiking);

        return new JsonResponse(['success' => true, 'hiking' => $hikingArray], 201);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Hikings')]
    public function delete(id $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $hiking = $entityManager->getRepository(Hiking::class)->find($id);
        $entityManager->remove($hiking);
        $entityManager->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/{id}/edit-general', name: 'edit_general', methods: ['PUT'])]
    #[OA\Tag(name: 'Hikings')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(type: 'object', 
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'detailedDescription', type: 'string'),
            ]))
    ]
    public function editGeneral(Request $request, id $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $hiking = $entityManager->getRepository(Hiking::class)->find($id);

        if (isset($data['name'])) $hiking->setName($data['name']);
        if (isset($data['description'])) $hiking->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $hiking->setDetailedDescription($data['detailedDescription']);

        $entityManager->flush();

        $hikingArray = $serializerService->serialize($hiking);

        return new JsonResponse(['success' => true, 'hiking' => $hikingArray]);
    }

    #[Route('/{id}/edit-tarifs', name: 'edit_tarifs', methods: ['PUT'])]
    #[OA\Tag(name: 'Hikings')]
    
    public function editTarifs(Request $request, id $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $hiking = $entityManager->getRepository(Hiking::class)->find($id);

        if (isset($data['tarifs'])) {
            // Assuming tarifs are handled separately and not part of this example
        }

        $entityManager->flush();

        $hikingArray = $serializerService->serialize($hiking);

        return new JsonResponse(['success' => true, 'hiking' => $hikingArray]);
    }

    #[Route('/{id}/edit-images', name: 'edit_images', methods: ['PUT'])]
    #[OA\Tag(name: 'Hikings')]
    public function editImages(Request $request, id $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $hiking = $entityManager->getRepository(Hiking::class)->find($id);

        if (isset($data['images'])) $hiking->setImages($data['images']);

        $entityManager->flush();

        $hikingArray = $serializerService->serialize($hiking);

        return new JsonResponse(['success' => true, 'hiking' => $hikingArray]);
    }

    #[Route('/{id}/edit-difficulty', name: 'edit_difficulty', methods: ['PUT'])]
    #[OA\Tag(name: 'Hikings')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(type: 'object', 
            properties: [
                new OA\Property(property: 'difficulty', type: 'string'),
            ]))
    ]
    public function editDifficulty(Request $request, id $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $hiking = $entityManager->getRepository(Hiking::class)->find($id);

        if (isset($data['difficulty'])) $hiking->setDifficulty($data['difficulty']);

        $entityManager->flush();

        $hikingArray = $serializerService->serialize($hiking);

        return new JsonResponse(['success' => true, 'hiking' => $hikingArray]);
    }
}
