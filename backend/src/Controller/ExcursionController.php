<?php

namespace App\Controller;

use App\Entity\Excursion;
use App\Repository\ExcursionRepository;
use App\Service\SerializerService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('/api/excursion')]
class ExcursionController extends AbstractController
{
    #[Route('/', name: 'excursion_index', methods: ['GET'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of excursions',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Excursion::class))
        )
    )]
    #[OA\Response(response: 404, description: 'Not found')]
    public function index(ExcursionRepository $excursionRepository, SerializerService $serializerService): JsonResponse
    {
        $excursions = $excursionRepository->findAll();
        $excursionsArray = $serializerService->serializeArray($excursions);

        return new JsonResponse(['success' => true, 'excursions' => $excursionsArray]);
    }

    
    #[Route('/{id}', name: 'excursion_show', methods: ['GET'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the excursion to retrieve',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns a single excursion',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
    public function show(int $id, ExcursionRepository $excursionRepository, SerializerService $serializerService): JsonResponse
    {
        $excursion = $excursionRepository->find($id);

        if (!$excursion) {
            return new JsonResponse(['success' => false, 'message' => 'Excursion not found'], 404);
        }

        $excursionArray = $serializerService->serialize($excursion);

        return new JsonResponse(['success' => true, 'excursion' => $excursionArray]);
    }

    #[Route('/', name: 'excursion_create', methods: ['POST'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'detailedDescription', type: 'string'),
                new OA\Property(property: 'images', type: 'string'),
                new OA\Property(property: 'startDate', type: 'string'),
                new OA\Property(property: 'endDate', type: 'string'),
                new OA\Property(property: 'destination', type: 'string'),
                new OA\Property(property: 'included', type: 'string'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Creates a new excursion',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
    public function create(Request $request, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        $excursion = new Excursion();
        if (isset($data['name'])) $excursion->setName($data['name']);
        if (isset($data['description'])) $excursion->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $excursion->setDetailedDescription($data['detailedDescription']);
        if (isset($data['images'])) $excursion->setImages($data['images']);
        if (isset($data['startDate'])) $excursion->setStartDate(new \DateTimeImmutable($data['startDate']));
        if (isset($data['endDate'])) $excursion->setEndDate(new \DateTimeImmutable($data['endDate']));
        if (isset($data['destination'])) $excursion->setDestination($data['destination']);
        if (isset($data['included'])) $excursion->setIncluded($data['included']);
        
        $entityManager->persist($excursion);
        $entityManager->flush();

        $excursionArray = $serializerService->serialize($excursion);

        return new JsonResponse(['success' => true, 'excursion' => $excursionArray], 201);
    }
    
    #[Route('/{id}', name: 'excursion_delete', methods: ['DELETE'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the excursion to delete',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\Response(
        response: 204,
        description: 'Deletes an excursion'
    )]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $excursion = $entityManager->getRepository(Excursion::class)->find($id);

        if (!$excursion) {
            return new JsonResponse(['success' => false, 'message' => 'Excursion not found'], 404);
        }

        $entityManager->remove($excursion);
        $entityManager->flush();

        return new JsonResponse(['success' => true, 'message' => 'Excursion deleted successfully'], 204);
    }

    #[Route('/{id}/edit-general', name: 'excursion_edit_general', methods: ['PUT'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the excursion to update',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'name', type: 'string', description: 'The name of the excursion'),
                new OA\Property(property: 'description', type: 'string', description: 'The description of the excursion'),
                new OA\Property(property: 'detailedDescription', type: 'string', description: 'The detailed description of the excursion'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Updates general information of an excursion',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
    public function editGeneral(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $excursion = $entityManager->getRepository(Excursion::class)->find($id);

        if (!$excursion) {
            return new JsonResponse(['success' => false, 'message' => 'Excursion not found'], 404);
        }

        if (isset($data['name'])) $excursion->setName($data['name']);
        if (isset($data['description'])) $excursion->setDescription($data['description']);
        if (isset($data['detailedDescription'])) $excursion->setDetailedDescription($data['detailedDescription']);

        $entityManager->flush();

        $excursionArray = $serializerService->serialize($excursion);

        return new JsonResponse(['success' => true, 'excursion' => $excursionArray]);
    }

    #[Route('/{id}/edit-tarifs', name: 'excursion_edit_tarifs', methods: ['PUT'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the excursion to update',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    // #TODO: Add request body
    #[OA\Response(
        response: 200,
        description: 'Updates tariffs for an excursion',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
    public function editTarifs(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $excursion = $entityManager->getRepository(Excursion::class)->find($id);

        if (!$excursion) {
            return new JsonResponse(['success' => false, 'message' => 'Excursion not found'], 404);
        }

        if (isset($data['tarifs'])) {
            // Handle the tariffs update logic here
        }

        $entityManager->flush();

        $excursionArray = $serializerService->serialize($excursion);

        return new JsonResponse(['success' => true, 'excursion' => $excursionArray]);
    }

    #[Route('/{id}/edit-images', name: 'excursion_edit_images', methods: ['PUT'])]
    #[OA\Tag(name: 'Excursion')]
    #[OA\Parameter(
        name: 'id',
        in: 'path',
        description: 'ID of the excursion to update',
        required: true,
        schema: new OA\Schema(type: 'integer')
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'images', type: 'array', items: new OA\Items(type: 'string'))
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Updates images for an excursion',
        content: new OA\JsonContent(ref: new Model(type: Excursion::class))
    )]
    public function editImages(Request $request, int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $excursion = $entityManager->getRepository(Excursion::class)->find($id);

        if (!$excursion) {
            return new JsonResponse(['success' => false, 'message' => 'Excursion not found'], 404);
        }

        if (isset($data['images'])) $excursion->setImages($data['images']);

        $entityManager->flush();

        $excursionArray = $serializerService->serialize($excursion);

        return new JsonResponse(['success' => true, 'excursion' => $excursionArray]);
    }
}
