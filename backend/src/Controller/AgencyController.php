<?php
namespace App\Controller;

use App\Entity\Agent;
use App\Service\SerializerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[Route('/api/agency', name: 'app_agency')]
class AgencyController extends AbstractController {
    #[Route('/', name: 'app_agency_index', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY')]
    #[OA\Tag(name: 'Agency')]
    public function index(EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $agencies = $entityManager->getRepository(Agency::class)->findAll();

        if (!$agencies) {
            return new JsonResponse(['success' => false, 'message' => 'No agencies found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'success' => true,
            'agencies' => $serializerService->serializeArray($agencies),
        ]);

    }
    #[Route('/addAgent', name: 'app_agency_add_agent', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY')]
    #[OA\Tag(name: 'Agency')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'name', type: 'string'),
                new OA\Property(property: 'phone', type: 'string', nullable: true),
            ],
        )
    )]
    public function addAgent(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            return new JsonResponse(['success' => false, 'message' => 'Email, password, name, and agency ID are required.'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $entityManager->getRepository(Agent::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['success' => false, 'message' => 'Email address already exists.'], Response::HTTP_CONFLICT);
        }

        $user = $this->getUser();
        if (!$user || !$user instanceof \App\Entity\Agency) {
            return new JsonResponse(['success' => false, 'message' => 'No authenticated agency found.'], Response::HTTP_UNAUTHORIZED);
        }

        $agent = new Agent();
        $agent->setEmail($data['email']);
        $agent->setName($data['name']);
        $agent->setPhone($data['phone'] ?? null);
        $agent->setPassword($passwordHasher->hashPassword($agent, $data['password']));
        $agent->setRoles(['ROLE_AGENT']);
        $agent->setAgency($user);

        $entityManager->persist($agent);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Agent added successfully!',
            'agent' => $serializerService->serialize($agent),
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_agency_get_agent', methods: ['GET'])]
    #[IsGranted('ROLE_AGENCY')]
    #[OA\Tag(name: 'Agency')]
    public function getAgent(int $id, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $agent = $entityManager->getRepository(Agent::class)->find($id);
        if (!$agent) {
            return new JsonResponse(['success' => false, 'message' => 'Agent not found.'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse([
            'success' => true,
            'agent' => $serializerService->serialize($agent),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_agency_edit_agent', methods: ['PUT'])]
    #[IsGranted('ROLE_AGENCY')]
    #[OA\Tag(name: 'Agency')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string', nullable: true),
                new OA\Property(property: 'name', type: 'string', nullable: true),
                new OA\Property(property: 'phone', type: 'string', nullable: true),
                new OA\Property(property: 'password', type: 'string', nullable: true),
            ],
        )
    )]
    public function editAgent(Request $request, int $id, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, SerializerService $serializerService): JsonResponse
    {
        $agent = $entityManager->getRepository(Agent::class)->find($id);
        if (!$agent) {
            return new JsonResponse(['success' => false, 'message' => 'Agent not found.'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $agent->setEmail($data['email']);
        }
        if (isset($data['name'])) {
            $agent->setName($data['name']);
        }
        if (isset($data['phone'])) {
            $agent->setPhone($data['phone']);
        }
        if (isset($data['password'])) {
            $agent->setPassword($passwordHasher->hashPassword($agent, $data['password']));
        }

        $entityManager->persist($agent);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Agent updated successfully!',
            'agent' => $serializerService->serialize($agent),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_agency_delete_agent', methods: ['DELETE'])]
    #[IsGranted('ROLE_AGENCY')]
    #[OA\Tag(name: 'Agency')]
    public function deleteAgent(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $agent = $entityManager->getRepository(Agent::class)->find($id);
        if (!$agent) {
            return new JsonResponse(['success' => false, 'message' => 'Agent not found.'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($agent);
        $entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'Agent deleted successfully!',
        ]);
    }
}