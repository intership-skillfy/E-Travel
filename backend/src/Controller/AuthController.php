<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Agency;
use App\Entity\Agent;
use App\Entity\Client;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use OpenApi\Attributes as OA;

class AuthController extends AbstractController
{
    #[Route('api/register', name: 'app_auth_register', methods: ['POST'])]
    #[OA\Post(
        path: "/api/register",
        tags: ["Authentication"],
        summary: "Register a new user",

    )]
    #[OA\RequestBody(
        required: true,
        description: "User registration data",
        content: new OA\JsonContent(
            type: "object",
            example: [
                "email" => "test@test.com",
                "password" => "test",
                "role" => "ROLE_ADMIN",
                "name" => "test",
            ]

        ),
    )]



    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $role = $data['role'] ?? null;

        if (!$data['email'] || !$data['password'] || !$role) {
            return new JsonResponse(['message' => 'Email, password, and role are required.'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email address already exists.'], Response::HTTP_CONFLICT);
        }

        if ($role === 'ROLE_ADMIN') {
            $user = new Admin();
        } elseif ($role === 'ROLE_CLIENT') {
            $user = new Client();
            $user->setProfilePic($data['profilePic']);
            $user->setPhone($data['phone']);
            $user->setPreferences($data['preferences']);
        } elseif ($role === 'ROLE_AGENCY') {
            $user = new Agency();
            $user->setPhone($data['phone']);
            $user->setAddresse($data['addresse']);
            $user->setLogoUrl($data['logoUrl']);
            $user->setWebsite($data['website']);
        } else {
            return new JsonResponse(['message' => 'Invalid role'], Response::HTTP_BAD_REQUEST);
        }

        $user->setEmail($data['email']);
        $user->setName($data['name']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles([$role]);

        $entityManager->persist($user);
        $entityManager->flush();

        try {
            $token = $jwtManager->create($user);
        } catch (BadCredentialsException $e) {
            return new JsonResponse(['message' => 'Failed to create JWT token.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'User created successfully !'], Response::HTTP_CREATED);
    }

    #[Route('/api/login', name: 'app_auth_login', methods: ['POST'])]
    #[OA\Post(
        path: "/api/login",
        tags: ["Authentication"],
        summary: "Login to the platform",

    )]

    #[OA\RequestBody(
        required: true,
        description: "User login data",
        content: new OA\JsonContent(
            type: "object",
            example: [
                "email" => "test@test.com",
                "password" => "test",

            ]

        ),


        
    )]

    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, UserRepository $repository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $repository->findOneBy(['email' => $data['email']]);
        if (!$existingUser) {
            return new JsonResponse(['message' => 'Invalid email.'], Response::HTTP_CONFLICT);
        }

        $user = $repository->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        $token = $jwtManager->create($user);

        return new JsonResponse(['token' => $token], Response::HTTP_OK);
    }
}
