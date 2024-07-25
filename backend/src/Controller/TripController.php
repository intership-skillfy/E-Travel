<?php

namespace App\Controller;

use App\Entity\Trip;
use App\Repository\TripRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/trips')]
class TripController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[OA\Tag(name: 'Trips')]
    public function index(TripRepository $tripRepository, SerializerService $serializerService): JsonResponse
    {
        $trips = $tripRepository->findAll();
        $tripsArray = $serializerService->serializeArray($trips);

        return new JsonResponse(['success' => true, 'trips' => $tripsArray]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Tag(name: 'Trips')]
    public function show(int $id, SerializerService $serializerService, EntityManagerInterface $entityManager): JsonResponse
    {
        $trip = $entityManager->getRepository(Trip::class)->find($id);
        if (!$trip) {
            return new JsonResponse(['success' => false, 'message' => 'Trip not found'], 404);
        }

        $tripArray = $serializerService->serialize($trip);

        return new JsonResponse(['success' => true, 'trip' => $tripArray]);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    #[OA\Tag(name: 'Trips')]
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
            ]
        )
    )]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $trip = new Trip();
        if (isset($data['name'])) $trip->setName($data['name']);
        if (isset($data['detailedDescription'])) $trip->setDetailedDescription($data['detailedDescription']);
        if (isset($data['images'])) $trip->setImages($data['images']);
        if (isset($data['startDate'])) $trip->setStartDate(new \DateTimeImmutable($data['startDate']));
        if (isset($data['endDate'])) $trip->setEndDate(new \DateTimeImmutable($data['endDate']));        
        $entityManager->persist($trip);
        $entityManager->flush();

        $tripArray = $serializerService->serialize($trip);

        return new JsonResponse(['success' => true, 'trip' => $tripArray], 201);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Trips')]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $trip = $entityManager->getRepository(Trip::class)->find($id);
        if (!$trip) {
            return new JsonResponse(['success' => false, 'message' => 'Trip not found'], 404);
        }

        $entityManager->remove($trip);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Trip deleted successfully']);
    }

    #[Route('/{id}/edit-general', name: 'edit_general', methods: ['PUT'])]
    #[OA\Tag(name: 'Trips')]
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

        $trip = $entityManager->getRepository(Trip::class)->find($id);
        if (!$trip) {
            return new JsonResponse(['success' => false, 'message' => 'Trip not found'], 404);
        }

        if (isset($data['name'])) $trip->setName($data['name']);
        if (isset($data['description'])) $trip->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $trip->setDetailedDescription($data['detailedDescription']);

        $entityManager->flush();

        $tripArray = $serializerService->serialize($trip);

        return new JsonResponse(['success' => true, 'trip' => $tripArray]);
    }

    #[Route('/{id}/edit-images', name: 'edit_images', methods: ['PUT'])]
    #[OA\Tag(name: 'Trips')]
    public function editImages(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $trip = $entityManager->getRepository(Trip::class)->find($id);
        if (!$trip) {
            return new JsonResponse(['success' => false, 'message' => 'Trip not found'], 404);
        }

        if (isset($data['images'])) $trip->setImages($data['images']);

        $entityManager->flush();

        $tripArray = $serializerService->serialize($trip);

        return new JsonResponse(['success' => true, 'trip' => $tripArray]);
    }
}
