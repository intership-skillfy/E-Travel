<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Offre;
use App\Repository\OffreRepository;
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

#[Route('/api/offer'), name('app_offer_')]
class OffreController extends AbstractController
{
    #[Route('/', name: 'app_offer_index', methods: ['GET'])]
    #[OA\Tag(name: 'Offer')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of categories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Offre::class, groups: ['full']))
        )
    )]
    public function index(OffreRepository $offreRepository, SerializerService $serializerService): JsonResponse
    {
        $offers = $offreRepository->findAll();
        $offersArray = $serializerService->serializeArray($offers);

        $responseArray = [
            'success' => true,
            'offers' => $offersArray,
        ];
        return new JsonResponse($responseArray);
    }
}