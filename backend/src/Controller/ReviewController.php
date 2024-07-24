<?php

namespace App\Controller;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use App\Repository\OffreRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/reviews')]
class ReviewController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/{offreId}', name: 'offre_reviews', methods: ['GET'])]
    #[OA\Tag(name: 'Review')]
    #[OA\Response(
        response: 200,
        description: 'Returns reviews of an offer',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Review::class))
        )
    )]
    public function index(ReviewRepository $reviewRepository, int $offreId): JsonResponse
    {
        $reviews = $reviewRepository->findBy(['offre' => $offreId]);
        $jsonContent = $this->serializer->serialize($reviews, 'json');
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/new', name: 'review_new', methods: ['POST'])]
    #[OA\Tag(name: 'Review')]
    #[OA\RequestBody(
        required: true,
        description: 'Add a review',
        content: new OA\JsonContent(
            type: 'object',
            example: [
                "offre_id" => 50,
                "rate" => 4,
                "comment" => "bad offer",
                "client_id" => 4,
            ]
        )
    )]
    public function new(Request $request, ClientRepository $clientRepository, OffreRepository $offreRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new JsonResponse(['error' => 'Invalid JSON data'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['offre_id'], $data['rate'], $data['comment'], $data['client_id'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $client = $clientRepository->find($data['client_id']);
        $offre = $offreRepository->find($data['offre_id']);

        if (!$client) {
            return new JsonResponse(['error' => 'Client not found'], Response::HTTP_NOT_FOUND);
        }

        if (!$offre) {
            return new JsonResponse(['error' => 'Offer not found'], Response::HTTP_NOT_FOUND);
        }

        $review = new Review();
        $review->setComment($data['comment']);
        $review->setRating($data['rate']);
        $review->setClient($client);
        $review->setOffre($offre);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        $jsonContent = $this->serializer->serialize($review, 'json');
        return new JsonResponse(['status' => 'Review created!', 'review' => $jsonContent], Response::HTTP_CREATED);
    }


    #[Route('/{id}', name: 'review_show', methods: ['GET'])]
    #[OA\Tag(name: 'Review')]
    #[OA\Response(
        response: 200,
        description: 'Returns a review',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Review::class , groups: ['full']))
        )
    )]
    public function show(int $id, ReviewRepository $reviewRepository): JsonResponse
    {
        $review = $reviewRepository->find($id);

        if (!$review) {
            return $this->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $this->serializer->serialize($review, 'json', ['groups' => 'full']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/edit/{id}', name: 'review_update', methods: ['PUT'])]
    #[OA\Tag(name: 'Review')]
    public function update(int $id, Request $request, ReviewRepository $reviewRepository, ClientRepository $clientRepository, OffreRepository $offreRepository): JsonResponse
    {
        $review = $reviewRepository->find($id);
        $data = json_decode($request->getContent(), true);

        if (!$review) {
            return $this->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $client = $clientRepository->find($data['client']);
        $offre = $offreRepository->find($data['offre']);

        if (!$client || !$offre) {
            return $this->json(['error' => 'Client or Offer not found'], Response::HTTP_NOT_FOUND);
        }

        $review->setComment($data['comment']);
        $review->setRating($data['rate']);
        $review->setClient($client);
        $review->setOffre($offre);

        $this->entityManager->flush();

        $jsonContent = $this->serializer->serialize($review, 'json', ['groups' => 'full']);
        return new JsonResponse(['status' => 'Review updated!', 'review' => $jsonContent], Response::HTTP_OK);
    }

    #[Route('/delete/{id}', name: 'review_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Review')]
    public function delete(int $id, ReviewRepository $reviewRepository): JsonResponse
    {
        $review = $reviewRepository->find($id);

        if (!$review) {
            return $this->json(['error' => 'Review not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($review);
        $this->entityManager->flush();

        return $this->json(['status' => 'Review deleted'], Response::HTTP_NO_CONTENT);
    }
}
