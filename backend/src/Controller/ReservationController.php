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
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $reservation = $serializer->deserialize($request->getContent(), Reservation::class, 'json');

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            return new JsonResponse($errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        $client = $clientRepository->find($data['client']);
        if (!$client) {
            return new JsonResponse(['message' => 'Client not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $reservation->setClient($client);

        // Get or create History
        $history = $client->getHistory();
        if (!$history) {
            $history = new History();
            $client->setHistory($history);
            $entityManager->persist($history);
        }
        // Add the reservation to the history
        $history->addReservation($reservation);
        $reservation->setHistory($history);

        $entityManager->persist($reservation);
        $entityManager->flush();

        return new JsonResponse($reservation, JsonResponse::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update_reservation', methods: ["PUT", "PATCH"])]
    #[OA\Tag(name: 'Reservations')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(ref: new Model(type: Reservation::class, groups: ['update']))
    )]
    #[OA\Response(
        response: 200,
        description: 'Reservation updated successfully',
        content: new OA\JsonContent(ref: new Model(type: Reservation::class, groups: ['full']))
    )]
    #[OA\Response(
        response: 404,
        description: 'Reservation not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Validation errors'
    )]
    public function updateReservation(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            return new JsonResponse(['message' => 'Reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Update reservation fields based on the input data
        if (isset($data['reservationDate'])) {
            $reservation->setReservationDate(new \DateTimeImmutable($data['reservationDate']));
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

        $errors = $validator->validate($reservation);
        if (count($errors) > 0) {
            return new JsonResponse($errors, JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();
        return new JsonResponse($reservation);
    }

    #[Route('/{id}', name: 'delete_reservation', methods: ["DELETE"])]
    #[OA\Tag(name: 'Reservations')]
    #[OA\Response(
        response: 204,
        description: 'Reservation deleted successfully'
    )]
    #[OA\Response(
        response: 404,
        description: 'Reservation not found'
    )]
    public function deleteReservation(
        int $id,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository
    ): JsonResponse {
        $reservation = $reservationRepository->find($id);
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

        return new JsonResponse(['message' => 'Reservation deleted successfully'], JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/{id}/status', name: 'update_reservation_status', methods: ["PATCH"])]
    #[OA\Tag(name: 'Reservations')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            example: [
                "status" => "confirmed",
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Reservation status updated successfully',
        content: new OA\JsonContent(ref: new Model(type: Reservation::class, groups: ['full']))
    )]
    #[OA\Response(
        response: 404,
        description: 'Reservation not found'
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid status'
    )]
    public function updateReservationStatus(
        Request $request,
        int $id,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $reservation = $reservationRepository->find($id);

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

        $entityManager->flush();
        return new JsonResponse($reservation);
    }
}
