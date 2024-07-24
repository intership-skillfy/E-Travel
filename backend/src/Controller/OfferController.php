<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Offre;
use App\Repository\OffreRepository;
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
    #[Route('/api/offre', name: 'app_offre_index', methods: ['GET'])]
    #[OA\Tag(name: 'Offre')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Offre::class, groups: ['full']))
        )
    )]
    public function index(OffreRepository $offreRepository): JsonResponse
    {
        $offres = $offreRepository->findAll();
        $offresArray = [];
        foreach ($offres as $offre) {
            $offresArray[] = [
                'id' => $offre->getId(),
                'title' => $offre->getTitle(),
                'category' => $offre->getCategory()->getName(),
                'description' => $offre->getDescription(),
                'startDate' => $offre->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $offre->getEndDate()->format('Y-m-d H:i:s'),
                'price' => $offre->getPrice(),
                'destination' => $offre->getDestination(),
                'updatedAt' => $offre->getUpdatedAt()->format('Y-m-d H:i:s'),
                'capacity' => $offre->getCapacity(),
            ];
        }

        $responseArray = [
            'success' => true,
            'offres' => $offresArray,
        ];
        return new JsonResponse($responseArray);
    }

    #[Route('/api/offre/new', name: 'app_offre_new', methods: ['POST'])]
    #[OA\Tag(name: 'Offre')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "title" => "Offre Title",
                "category" => 1,
                "description" => "Offre Description",
                "startDate" => "2024-07-17T10:00:00Z",
                "endDate" => "2024-07-20T18:00:00Z",
                "price" => 100,
                "destination" => "Offre Destination",
                "capacity" => 50,
            ],
        )
    )]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
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

        $offre = new Offre();
        // $offre->setTitle($requestData['title']);
        // $offre->setCategory($category);
        // $offre->setDescription($requestData['description']);
        // $offre->setStartDate(new \DateTimeImmutable($requestData['startDate']));
        // $offre->setEndDate(new \DateTimeImmutable($requestData['endDate']));
        // $offre->setPrice($requestData['price']);
        // $offre->setDestination($requestData['destination']);
        // $offre->setUpdatedAt(new \DateTimeImmutable());
        // $offre->setCapacity($requestData['capacity']);
        // $offre->setAgency(null);

        $category->addOffre($offre);

        $entityManager->persist($offre);
        $entityManager->flush();

        // $responseArray = [
        //     'success' => true,
        //     'offre' => [
        //         'id' => $offre->getId(),
        //         'title' => $offre->getTitle(),
        //         'category' => $category->getName(),
        //         'description' => $offre->getDescription(),
        //         'startDate' => $offre->getStartDate()->format('Y-m-d H:i:s'),
        //         'endDate' => $offre->getEndDate()->format('Y-m-d H:i:s'),
        //         'price' => $offre->getPrice(),
        //         'destination' => $offre->getDestination(),
        //         'updatedAt' => $offre->getUpdatedAt()->format('Y-m-d H:i:s'),
        //         'capacity' => $offre->getCapacity(),
        //     ],
        // ];
        // return new JsonResponse($responseArray);
    }

    #[Route('/api/offre/edit/{id}', name: 'app_offre_edit', methods: ['PUT'])]
    #[OA\Tag(name: 'Offre')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "title" => "Offre Title",
                "category" => 1,
                "description" => "Offre Description",
                "startDate" => "2024-07-17T10:00:00Z",
                "endDate" => "2024-07-20T18:00:00Z",
                "price" => 100,
                "destination" => "Offre Destination",
                "capacity" => 50,
            ],
        )
    )]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
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

        $offre = $entityManager->getRepository(Offre::class)->find($id);
        if (!$offre) {
            return new JsonResponse(['error' => 'Offre not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $category = $entityManager->getRepository(Category::class)->find($requestData['category']);
        if (!$category) {
            return new JsonResponse(['error' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $offre->setTitle($requestData['title']);
        $offre->setCategory($category);
        $offre->setDescription($requestData['description']);
        $offre->setStartDate(new \DateTimeImmutable($requestData['startDate']));
        $offre->setEndDate(new \DateTimeImmutable($requestData['endDate']));
        $offre->setPrice($requestData['price']);
        $offre->setDestination($requestData['destination']);
        $offre->setUpdatedAt(new \DateTimeImmutable());
        $offre->setCapacity($requestData['capacity']);

        $entityManager->persist($offre);
        $entityManager->flush();

        $responseArray = [
            'success' => true,
            'offre' => [
                'id' => $offre->getId(),
                'title' => $offre->getTitle(),
                'category' => $offre->getCategory()->getName(),
                'description' => $offre->getDescription(),
                'startDate' => $offre->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $offre->getEndDate()->format('Y-m-d H:i:s'),
                'price' => $offre->getPrice(),
                'destination' => $offre->getDestination(),
                'updatedAt' => $offre->getUpdatedAt()->format('Y-m-d H:i:s'),
                'capacity' => $offre->getCapacity(),
            ],
        ];
        return new JsonResponse($responseArray);
    }

    #[Route('/api/offre/delete/{id}', name: 'app_offre_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Offre')]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $offre = $entityManager->getRepository(Offre::class)->find($id);
        if (!$offre) {
            return new JsonResponse(['error' => 'Offre not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $entityManager->remove($offre);
        $entityManager->flush();
        return new JsonResponse(['success' => true]);
    }

}
