<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Agency;
use App\Entity\Agent;
use App\Entity\Client;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use App\Service\RefreshTokenService;
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



    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository, RefreshTokenService $refreshTokenService,FileUploader $fileUploader): JsonResponse
    {
        $data = $request->request->all();
        $role = $data['role'] ?? null;

        // Validate the presence of email, password, and role
        if (!$data['email'] || !$data['password'] || !$role) {
            return new JsonResponse(['message' => 'Email, password, and role are required.'], Response::HTTP_BAD_REQUEST);
        }

        // Check if the email already exists
        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse(['message' => 'Email address already exists.'], Response::HTTP_CONFLICT);
        }

        // Create the appropriate entity based on the role
        if ($role === 'ROLE_ADMIN') {
            $user = new Admin();
        } elseif ($role === 'ROLE_CLIENT') {
            $user = new Client();
            if ($request->files->has('profilePic')) {
                $profilePic = $fileUploader->upload($request->files->get('profilePic'));
                $user->setProfilePic($profilePic);
            }
            $user->setPhone($data['phone']);
            $user->setPreferences($data['preferences']);
        } elseif ($role === 'ROLE_AGENCY') {
            $user = new Agency();
            if ($request->files->has('logoUrl')) {
                $logoUrl = $fileUploader->upload($request->files->get('logoUrl'));
                $user->setLogoUrl($logoUrl);
            }
            $user->setPhone($data['phone']);
            $user->setAddresse($data['addresse']);
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

        // Generate JWT token
        try {
            $token = $jwtManager->create($user);
            $refreshToken = $refreshTokenService->createRefreshToken($user);

        } catch (BadCredentialsException $e) {
            return new JsonResponse(['message' => 'Failed to create JWT token.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'message' => 'User created successfully !',
            'token' => $token,
            'refreshToken' => $refreshToken
        ], Response::HTTP_CREATED);
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

    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, UserRepository $repository,RefreshTokenService $refreshTokenService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate request data
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $existingUser = $repository->findOneBy(['email' => $data['email']]);
        if (!$existingUser) {
            return new JsonResponse(['message' => 'Invalid email.'], Response::HTTP_CONFLICT);
        }

        // Attempt to fetch the user from database
        $user = $repository->findOneBy(['email' => $data['email']]);

        // If user not found or password incorrect, return error
        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        // Generate JWT token
        $token = $jwtManager->create($user);
        $refreshToken = $refreshTokenService->createRefreshToken($user);

        // Return token to the client
        return new JsonResponse([
            'token' => $token,
            'refresh_token' => $refreshToken
        ], Response::HTTP_OK);
    }

    #[Route('/api/token/refresh', name: 'app_token_refresh', methods: ['POST'])]
    #[OA\Post(
        path: "/api/token/refresh",
        tags: ["Authentication"],
        summary: "Refresh JWT token using refresh token",
    )]
    #[OA\RequestBody(
        required: true,
        description: "Refresh token data",
        content: new OA\JsonContent(
            type: "object",
            example: [
                "refresh_token" => "your-refresh-token-here",
            ]
        ),
    )]
    public function refresh(Request $request, JWTTokenManagerInterface $jwtManager, RefreshTokenService $refreshTokenService, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['refresh_token'])) {
            return new JsonResponse(['message' => 'Refresh token is required.'], Response::HTTP_BAD_REQUEST);
        }

        $payload = $refreshTokenService->decodeRefreshToken($data['refresh_token']);
        if (!$payload || !$refreshTokenService->isRefreshTokenValid($payload)) {
            return new JsonResponse(['message' => 'Invalid or expired refresh token.'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $userRepository->findOneBy(['email' => $payload['email']]);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $jwtManager->create($user);
        $newRefreshToken = $refreshTokenService->createRefreshToken($user);

        return new JsonResponse(['token' => $token, 'refresh_token' => $newRefreshToken], Response::HTTP_OK);
    }
}
