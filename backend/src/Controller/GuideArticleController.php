<?php

namespace App\Controller;

use App\Entity\GuideArticle;
use App\Repository\GuideArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('api/guideArticles')]
class GuideArticleController extends AbstractController
{
    private SerializerInterface $serializer;
    public function __construct(
        
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'create_guide_article', methods: ['POST'])]
    #[OA\Tag(name: 'GuideArticle')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "title" => "aaaa",
                "content" => "jfafaenfp",

            ],
        ),
    )]

    public function create(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $data = $request->getContent();
        
        $guideArticle = $serializer->deserialize($data, GuideArticle::class, 'json');
        
        $errors = $validator->validate($guideArticle);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $guideArticle->setCreatedAt(new \DateTimeImmutable());
        $guideArticle->setUpdatedAt(new \DateTimeImmutable());
        
        $em->persist($guideArticle);
        $em->flush();

        return $this->json($guideArticle, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'get_guide_article', methods: ['GET'])]
    #[OA\Tag(name: 'GuideArticle')]
    #[OA\Response(
        response: 200,
        description: 'Returns the guide article',
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "id" => 1,
                "title" => "aaaa",
                "content" => "jfafaenfp",
                "createdAt" => "2024-07-23T10:00:00+00:00",
                "updatedAt" => "2024-07-23T10:00:00+00:00",
                "images" => [],
                "agency" => null,
                "admin_article" => null,
                "client" => [
                                "id" => 1,
                                "name" => "Client Name"
                            ]            ],
            ),
    )]
    public function show(int $id, GuideArticleRepository $repository): JsonResponse
    {
        $guideArticle = $repository->find($id);

        if (!$guideArticle) {
            return $this->json(['message' => 'Guide article not found'], Response::HTTP_NOT_FOUND);
        }

        $jsonContent = $this->serializer->serialize($guideArticle, 'json', ['groups' => 'full']);
        return new JsonResponse($jsonContent, 200, [], true);
    }

    #[Route('/{id}', name: 'update_guide_article', methods: ["PUT"])]
    #[OA\Tag(name: 'GuideArticle')]
    #[OA\RequestBody(
        required: true,
        description: 'Returns the guide article',
        content: new OA\JsonContent(
            type: Object::class,
            example: [
                "id" => 1,
                "title" => "aaaa",
                "content" => "jfafaenfp",
                "createdAt" => "2024-07-23T10:00:00+00:00",
                "updatedAt" => "2024-07-23T10:00:00+00:00",
                "images" => [],
                "agency" => null,
                "admin_article" => null,
                "client" => [
                                "id" => 1,
                                "name" => "Client Name"
                            ]            ],
            ),
    )]
    public function update(int $id, Request $request, GuideArticleRepository $repository, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $guideArticle = $repository->find($id);
        
        if (!$guideArticle) {
            return $this->json(['message' => 'Guide article not found'], Response::HTTP_NOT_FOUND);
        }
        
        $data = $request->getContent();
        
        $serializer->deserialize($data, GuideArticle::class, 'json', ['object_to_populate' => $guideArticle]);
        
        $errors = $validator->validate($guideArticle);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $guideArticle->setUpdatedAt(new \DateTimeImmutable());
        
        $em->persist($guideArticle);
        $em->flush();

        $jsonContent = $this->serializer->serialize($guideArticle, 'json', ['groups' => 'full']);
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);    }

    #[Route('/{id}', name: 'delete_guide_article', methods: ['DELETE'])]
    #[OA\Tag(name: 'GuideArticle')]
    

    public function delete(int $id, GuideArticleRepository $repository, EntityManagerInterface $em): Response
    {
        $guideArticle = $repository->find($id);
        
        if (!$guideArticle) {
            return $this->json(['message' => 'Guide article not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($guideArticle);
        $em->flush();

        return $this->json(['message' => 'Guide article deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
