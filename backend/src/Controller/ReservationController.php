<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Client;
use App\Entity\History;
use App\Repository\ReservationRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/reservations')]

#[Route('/api/reservations')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'list_reservations', methods: ["GET"])]
    #[OA\Tag(name: 'Reservations')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of reservations',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Reservation::class, groups: ['full']))
        )
    )]
    public function listReservations(ReservationRepository $reservationRepository): JsonResponse
    {
        $reservations = $reservationRepository->findAll();
        return new JsonResponse(['success' => true, 'reservations' => $reservations]);
    }

    #[Route('/{id}', name: 'get_reservation', methods: ["GET"])]
    #[OA\Tag(name: 'Reservations')]
    #[OA\Response(
        response: 200,
        description: 'Returns the details of a reservation',
        content: new OA\JsonContent(ref: new Model(type: Reservation::class, groups: ['full']))
    )]
    #[OA\Response(
        response: 404,
        description: 'Reservation not found'
    )]
    public function getReservation(int $id, ReservationRepository $reservationRepository): JsonResponse
    {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            return new JsonResponse(['message' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        return new JsonResponse($reservation);
    }

    #[Route('/', name: 'create_reservation', methods: ["POST"])]
    #[OA\Tag(name: 'Reservations')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Reservation::class, groups: ['create']))
    )]
    #[OA\Response(
        response: 201,
        description: 'Reservation created successfully',
        content: new OA\JsonContent(ref: new Model(type: Reservation::class, groups: ['full']))
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation errors'
    )]
    public function createReservation(
        Request $request,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository,
        ClientRepository $clientRepository,
        ValidatorInterface $validator,
        SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
        $this->reservationRepository = $reservationRepository;
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }


    #[Route('/', name: 'list_reservations', methods: ["GET"])]
    #[OA\Tag(name: 'Reservation')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of reservations',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Reservation::class, groups: ['full']))
        )
    )]
    public function listReservations(): JsonResponse
    {
        $reservations = $this->reservationRepository->findAll();

        // Serialize the list of reservations
        $jsonContent = $this->serializer->serialize($reservations, 'json', ['groups' => 'full']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/{id}', name: 'get_reservation', methods: ["GET"])]
    #[OA\Tag(name:'Reservation')]
    #[OA\Response(
        response: 200,
        description: 'Returns a reservation',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Reservation::class, groups: ['full']))
        )
    )]

    public function getReservation(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['message' => 'Reservation not found'], 404);
        }

        $jsonContent = $this->serializer->serialize($reservation, 'json', ['groups' => 'full']);
        return new JsonResponse($jsonContent,200, [], true);
    }

    #[Route('/', name: 'create_reservation', methods: ["POST"])]
    #[OA\Tag(name:'Reservation')]
    #[OA\RequestBody(
        required: true,
        description: 'create a reservation',
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "reservationDate" => "2024-07-17T10:00:00Z",
                "amount" => 1000,
                "status" => "confirmed",
                "nbrperson" => 3,
            ],        )
    )]

    public function createReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = $this->serializer->deserialize($request->getContent(), Reservation::class, 'json');
    
        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }
    
        $client = $this->clientRepository->find($data['client_id']);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $reservation->setClient($client);
    
        $history = $client->getHistory();
        if (!$history) {
            $history = new History();
            $client->setHistory($history);
            $entityManager->persist($history);
        }
    
        $history->addReservation($reservation);
        $reservation->setHistory($history);
    
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();
    
        $jsonContent = $this->serializer->serialize($reservation, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId(); // or any other unique identifier
            },
            'groups' => ['full']
        ]);
    
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/{id}', name: 'update_reservation', methods: ["PUT"])]
    #[OA\Tag(name:'Reservation')]
    #[OA\RequestBody(
        required: true,
        description: 'create a reservation',
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "reservationDate" => "2024-07-17T10:00:00Z",
                "amount" => 1000,
                "status" => "canceled",
                "nbrperson" => 3,
            ]        )
    )]
    public function updateReservation(Request $request, int $id): JsonResponse
{
    $reservation = $this->reservationRepository->find($id);
    if (!$reservation) {
        return $this->json(['message' => 'Reservation not found'], 404);
    }

    $data = json_decode($request->getContent(), true);

    // Update reservation fields based on the input data
    if (isset($data['reservationDate'])) {
        try {
            $reservation->setReservationDate(new \DateTimeImmutable($data['reservationDate']));
        } catch (\Exception $e) {
            return $this->json(['message' => 'Invalid date format'], 400);
        }
    }
    if (isset($data['amount'])) {
        $reservation->setAmount($data['amount']);
    }
    if (isset($data['status'])) {
        $reservation->setStatus($data['status']);
    }
    if (isset($data['nbr_person'])) {
        $reservation->setNbrPerson($data['nbr_person']);
    }

    $errors = $this->validator->validate($reservation);
    if (count($errors) > 0) {
        // Format validation errors
        $errorMessages = [];
        foreach ($errors as $error) {
            $errorMessages[] = $error->getMessage();
        }
        return $this->json(['errors' => $errorMessages], 400);
    }

    $this->entityManager->flush();

    // Serialize using a group to avoid circular references
    $jsonContent = $this->serializer->serialize($reservation, 'json', ['groups' => ['full']]);

    return new JsonResponse($jsonContent, 200, [], true);
}

    #[Route('/{id}', name: 'delete_reservation', methods: ["DELETE"])]
    #[OA\Tag(name: 'Reservation')]

    public function deleteReservation(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return new JsonResponse(['message' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Remove the reservation from history
        $history = $reservation->getHistory();
        if ($history) {
            $history->removeReservation($reservation);
        }

        // Remove the reservation
        $entityManager->remove($reservation);
        $entityManager->flush();

        return $this->json(['message' => 'Reservation deleted successfully'], 200);
    }

    #[Route('/{id}/status', name: 'update_reservation_status', methods: ["PUT"])]
    #[OA\Tag(name: 'Reservation')]
    #[OA\RequestBody(
        required: true,
        description: 'update status',
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "reservationDate" => "2024-07-17T10:00:00Z",
                "amount" => 1000,
                "status" => "canceled",
                "nbrperson" => 3,
            ]        )
    )]

    public function updateReservationStatus(Request $request, int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        $newStatus = $data['status'] ?? null;

        $allowedStatuses = ['confirmed', 'canceled', 'pending'];

        if (!in_array($newStatus, $allowedStatuses, true)) {
            return new JsonResponse(['error' => 'Invalid status'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $reservation->setStatus($newStatus);

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            return new JsonResponse($errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();
        return new JsonResponse(['message' => 'Status updated successfully'], 200);
    }
}
