<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Offer;
use App\Repository\OfferRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\Serializer\Annotation\Groups;

class OfferController extends AbstractController
{
    #[Route('/api/offer', name: 'app_offer_index', methods: ['GET'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Offer::class, groups: ['full']))
        )
    )]
    public function index(OfferRepository $offerRepository, SerializerService $serializerService): JsonResponse
    {
        $offers = $offerRepository->findAll();
        $offersArray = $serializerService->serializeArray($offers);

        $responseArray = [
            'success' => true,
            'offers' => $offersArray,
        ];
        return new JsonResponse($responseArray);
    }

    #[Route('/api/offer/new', name: 'app_offer_new', methods: ['POST'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "title" => "Offer Title",
                "category" => 1,
                "description" => "Offer Description",
                "startDate" => "2024-07-17T10:00:00Z",
                "endDate" => "2024-07-20T18:00:00Z",
                "price" => 100,
                "destination" => "Offer Destination",
                "capacity" => 50,
            ],
        )
    )]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $missingFields = [];
        if (!isset($requestData['title'])) {
            $missingFields[] = 'title';
        }
        if (!isset($requestData['category'])) {
            $missingFields[] = 'category';
        }
        if (!isset($requestData['description'])) {
            $missingFields[] = 'description';
        }
        if (!isset($requestData['startDate'])) {
            $missingFields[] = 'startDate';
        }
        if (!isset($requestData['endDate'])) {
            $missingFields[] = 'endDate';
        }
        if (!isset($requestData['price'])) {
            $missingFields[] = 'price';
        }
        if (!isset($requestData['destination'])) {
            $missingFields[] = 'destination';
        }
        if (!isset($requestData['capacity'])) {
            $missingFields[] = 'capacity';
        }

        if (!empty($missingFields)) {
            return new JsonResponse(['error' => 'Missing required fields: ' . implode(', ', $missingFields)], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Fetch category from repository
        $category = $entityManager->getRepository(Category::class)->find($requestData['category']);

        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $offer = new Offer();
        $offer->setTitle($requestData['title']);
        $offer->setCategory($category);
        $offer->setDescription($requestData['description']);
        $offer->setStartDate(new \DateTimeImmutable($requestData['startDate']));
        $offer->setEndDate(new \DateTimeImmutable($requestData['endDate']));
        $offer->setPrice($requestData['price']);
        $offer->setDestination($requestData['destination']);
        $offer->setUpdatedAt(new \DateTimeImmutable());
        $offer->setCapacity($requestData['capacity']);
        $offer->setAgency(null);

        $category->addOffer($offer);

        $entityManager->persist($offer);
        $entityManager->flush();

        $responseArray = [
            'success' => true,
            'offer' => $serializerService->serialize($offer),
        ];
        return new JsonResponse($responseArray);
    }

    #[Route('/api/offer/edit/{id}', name: 'app_offer_edit', methods: ['PUT'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "title" => "Offer Title",
                "category" => 1,
                "description" => "Offer Description",
                "startDate" => "2024-07-17T10:00:00Z",
                "endDate" => "2024-07-20T18:00:00Z",
                "price" => 100,
                "destination" => "Offer Destination",
                "capacity" => 50,
            ],
        )
    )]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id, SerializerService $serializerService): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        $missingFields = [];
        if (!isset($requestData['title'])) {
            $missingFields[] = 'title';
        }
        if (!isset($requestData['category'])) {
            $missingFields[] = 'category';
        }
        if (!isset($requestData['description'])) {
            $missingFields[] = 'description';
        }
        if (!isset($requestData['startDate'])) {
            $missingFields[] = 'startDate';
        }
        if (!isset($requestData['endDate'])) {
            $missingFields[] = 'endDate';
        }
        if (!isset($requestData['price'])) {
            $missingFields[] = 'price';
        }
        if (!isset($requestData['destination'])) {
            $missingFields[] = 'destination';
        }
        if (!isset($requestData['capacity'])) {
            $missingFields[] = 'capacity';
        }

        $offer = $entityManager->getRepository(Offer::class)->find($id);
        if (!$offer) {
            return new JsonResponse(['error' => 'Offer not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $category = $entityManager->getRepository(Category::class)->find($requestData['category']);
        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $offer->setTitle($requestData['title']);
        $offer->setCategory($category);
        $offer->setDescription($requestData['description']);
        $offer->setStartDate(new \DateTimeImmutable($requestData['startDate']));
        $offer->setEndDate(new \DateTimeImmutable($requestData['endDate']));
        $offer->setPrice($requestData['price']);
        $offer->setDestination($requestData['destination']);
        $offer->setUpdatedAt(new \DateTimeImmutable());
        $offer->setCapacity($requestData['capacity']);

        $entityManager->persist($offer);
        $entityManager->flush();

        $responseArray = [
            'success' => true,
            'offer' => $serializerService->serialize($offer),
        ];
        return new JsonResponse($responseArray);
    }

    #[Route('/api/offer/delete/{id}', name: 'app_offer_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Offer')]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $offer = $entityManager->getRepository(Offer::class)->find($id);
        if (!$offer) {
            return new JsonResponse(['error' => 'Offer not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($offer);
        $entityManager->flush();
        return new JsonResponse(['success' => true]);
    }

}