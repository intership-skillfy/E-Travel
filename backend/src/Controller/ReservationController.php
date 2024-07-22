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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ReservationController extends AbstractController
{
    private ReservationRepository $reservationRepository;
    private ClientRepository $clientRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private SerializerInterface $serializer;

    public function __construct(
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


    #[Route('/reservations', name: 'list_reservations', methods: ["GET"])]
    public function listReservations(): JsonResponse
    {
        $reservations = $this->reservationRepository->findAll();
        return $this->json($reservations, 200, []);
    }

    #[Route('/reservations/{id}', name: 'get_reservation', methods: ["GET"])]
    public function getReservation(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['message' => 'Reservation not found'], 404);
        }
        return $this->json($reservation, 200, []);
    }

    #[Route('/reservations', name: 'create_reservation', methods: ["POST"])]
    public function createReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = $this->serializer->deserialize($request->getContent(), Reservation::class, 'json');

        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $client = $this->clientRepository->find($data['client']);
        if (!$client) {
            return $this->json(['message' => 'Client not found'], 404);
        }

        $reservation->setClient($client);

        // Get or create History
        $history = $client->getHistory();
        if (!$history) {
            $history = new History();
            $client->setHistory($history);
            $this->entityManager->persist($history);
        }
        // Add the reservation to the history
        $history->addReservation($reservation);
        $reservation->setHistory($history);

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $this->json($reservation, 200, []);
    }

    #[Route('/reservations/{id}', name: 'update_reservation', methods: ["PUT", "PATCH"])]
    public function updateReservation(Request $request, int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['message' => 'Reservation not found'], 404);
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

        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $this->entityManager->flush();
        return $this->json($reservation, 200, []);
    }

    #[Route('/reservations/{id}', name: 'delete_reservation', methods: ["DELETE"])]
    public function deleteReservation(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['message' => 'Reservation not found'], 404);
        }

        // Remove the reservation from history
        $history = $reservation->getHistory();
        if ($history) {
            $history->removeReservation($reservation);
        }

        // Remove the reservation
        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return $this->json(['message' => 'Reservation deleted successfully'], 204);
    }

    #[Route('/reservations/{id}/status', name: 'update_reservation_status', methods: ["PATCH"])]
    public function updateReservationStatus(Request $request, int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        $data = json_decode($request->getContent(), true);
        $newStatus = $data['status'] ?? null;

        $allowedStatuses = ['confirmed', 'canceled', 'pending'];

        if (!in_array($newStatus, $allowedStatuses, true)) {
            return $this->json(['error' => 'Invalid status'], 400);
        }

        $reservation->setStatus($newStatus);

        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $this->entityManager->flush();
        return $this->json($reservation, 200, []);
    }
}
