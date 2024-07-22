<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    #[OA\Tag(name: 'Category')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Category::class, groups: ['full']))
        )
    )]
    public function index(CategoryRepository $categoryRepository, SerializerService $serializerService): JsonResponse
    {
        $categories = $categoryRepository->findAll();
        if (empty($categories)) {
            return new JsonResponse(['success' => false, 'message' => 'No categories found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $categoriesArray = $serializerService->serializeArray($categories);

        $responseArray = [
            'success' => true,
            'categories' => $categoriesArray,
        ];

        return new JsonResponse($responseArray);
    }

    #[Route('/new', name: 'app_category_new', methods: ['POST'])]
    #[OA\Tag(name: 'Category')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "name" => "Category Name",
            ],
        ),
    )]
    public function new(Request $request, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);


        if (!isset($requestData['name'])) {
            return new JsonResponse(['success' => false, 'message' => 'Name is required'], 400);
        }


        $category = new Category();
        $category->setName($requestData['name']);

        $entityManager->persist($category);
        $entityManager->flush();

        $responseData = [
            'success' => true,
            'message' => 'Category created successfully',
            'category' => $serializerService->serialize($category),
        ];

        return new JsonResponse($responseData);
    }

    #[Route('/edit/{id}', name: 'app_category_edit', methods: ['PUT'])]
    #[OA\Tag(name: 'Category')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "name" => "Category Name",
            ],
        ),
    )]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['success' => false, 'message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }
        
        $requestData = json_decode($request->getContent(), true);

        if (!isset($requestData['name'])) {
            return new JsonResponse(['success' => false, 'message' => 'Name is required'], 400);
        }


        $category->setName($requestData['name']);


        $entityManager->persist($category);
        $entityManager->flush();

        $responseData = [
            'success' => true,
            'message' => 'Category updated successfully',
            'category' => [
                'id' => $category->getId(),
                'name' => $category->getName(),
            ],
        ];

        return new JsonResponse($responseData);
    }

    #[Route('/addoffer/{id}', name: 'app_category_addoffer', methods: ['POST'])]
    #[OA\Tag(name: 'Category')]
    public function addOffer(Request $request, EntityManagerInterface $entityManager, Category $category): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);


        $offerId = $requestData['offer'];
        $offer = $entityManager->getRepository(Offer::class)->find($offerId);

        if (!$offer) {
            return new JsonResponse(['success' => false, 'message' => 'Offer not found'], 404);
        }

        $category->addOffer($offer);

        $entityManager->persist($category);
        $entityManager->flush();

        $responseData = [
            'success' => true,
            'message' => 'Offer added to category successfully',
            'category' => [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'offers' => $category->getOffers()->toArray(),
            ],
        ];

        return new JsonResponse($responseData);
    }

    #[Route('/delete/{id}', name: 'app_category_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Category')]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            return new JsonResponse(['success' => false, 'message' => 'Category not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($category);
        $entityManager->flush();

        $responseData = [
            'success' => true,
            'message' => 'Category deleted successfully',
        ];

        return new JsonResponse($responseData);
    }
}
