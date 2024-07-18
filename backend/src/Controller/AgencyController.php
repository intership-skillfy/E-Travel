<?php

namespace App\Controller;

use App\Entity\Agent;
use App\Repository\AgencyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AgencyController extends AbstractController
{
    #[Route('api/agency/addAgent', name: 'app_agency_add_agent', methods: ['POST'])]
    #[IsGranted('ROLE_AGENCY')]
    public function addAgent(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, AgencyRepository $agencyRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate request data
        if (!isset($data['email']) || !isset($data['password']) || !isset($data['name'])) {
            return new JsonResponse(['message' => 'Email, password, name, and agency ID are required.'], Response::HTTP_BAD_REQUEST);
        }

        // Check if the email already exists
        $existingUser = $entityManager->getRepository(Agent::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email address already exists.'], Response::HTTP_CONFLICT);
        }

         // Retrieve the authenticated agency
         $user = $this->getUser();
        //  dd($user);

         // Debug: Check the authenticated user
         if (!$user) {
             return new JsonResponse(['message' => 'No authenticated user found.'], Response::HTTP_UNAUTHORIZED);
         }
 
         // Debug: Check the user roles
         if (!in_array('ROLE_AGENCY', $user->getRoles(), true)) {
             return new JsonResponse(['message' => 'Authenticated user does not have ROLE_AGENCY.'], Response::HTTP_FORBIDDEN);
         }
 
         if (!$user instanceof \App\Entity\Agency) {
             return new JsonResponse(['message' => 'Authenticated user is not an agency.'], Response::HTTP_FORBIDDEN);
         }
         $agency = $user;


        // Create a new Agent entity
        $agent = new Agent();
        $agent->setEmail($data['email']);
        $agent->setName($data['name']);
        $agent->setPhone($data['phone']);
        $agent->setPassword($passwordHasher->hashPassword($agent, $data['password']));
        $agent->setRoles(['ROLE_AGENT']);
        $agent->setAgency($agency);

        // Persist the agent entity
        $entityManager->persist($agent);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Agent added successfully!'], Response::HTTP_CREATED);
    }
}
