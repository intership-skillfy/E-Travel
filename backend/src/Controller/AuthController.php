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
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;



class AuthController extends AbstractController
{
    #[Route('/api/currentUser', name: 'me', methods: ['GET'])]
    public function me(Security $security): JsonResponse
    {   
        $currentUser = $security->getUser();

        return new JsonResponse([
            'id' => $currentUser?->getId(),
            'email' => $currentUser?->getEmail(),
        ]);

    }

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
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager, 
        JWTTokenManagerInterface $jwtManager, 
        UserRepository $userRepository, 
        RefreshTokenService $refreshTokenService,
        FileUploader $fileUploader,
        LoggerInterface $logger
    ): JsonResponse {
        
        $rawContent = $request->getContent();
        $logger->info('Raw request content', ['content' => $rawContent]);
    
        // Check if there's JSON data in the 'json' field of the request
        $jsonData = $request->request->get('json');
        if ($jsonData) {
            $data = json_decode($jsonData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $logger->error('JSON decode error', ['error' => json_last_error_msg(), 'content' => $jsonData]);
                return new JsonResponse(['message' => 'Invalid JSON data.'], Response::HTTP_BAD_REQUEST);
            }
        } else {
            // If no JSON data, use the request data directly
            $data = $request->request->all();
        }
    
    
       $logger->info('Decoded registration data', $data);
    
        $role = $data['role'] ?? null;
    
        // Validate the presence of email, password, and role
        if (empty($data['email']) || empty($data['password']) || !$role) {
           $logger->error('Missing email, password, or role', $data);
            return new JsonResponse(['message' => 'Email, password, and role are required.'], Response::HTTP_BAD_REQUEST);
        }
    
        // Check if the email already exists
        $existingUser = $userRepository->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
           $logger->error('Email address already exists', ['email' => $data['email']]);
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
            $user->setPhone($data['phone'] ?? '');
        } elseif ($role === 'ROLE_AGENCY') {
            $user = new Agency();
            if ($request->files->has('logoUrl')) {
                $logoUrl = $fileUploader->upload($request->files->get('logoUrl'));
                $user->setLogoUrl($logoUrl);
            }
            $user->setPhone($data['phone'] ?? '');
            $user->setAddresse($data['addresse'] ?? '');
            $user->setWebsite($data['website'] ?? '');
        } else {
           $logger->error('Invalid role', ['role' => $role]);
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
           $logger->info('JWT token created successfully', ['token' => $token]);
        } catch (BadCredentialsException $e) {
           $logger->error('Failed to create JWT token', ['exception' => $e->getMessage()]);
            return new JsonResponse(['message' => 'Failed to create JWT token.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    
       $logger->info('User created successfully', ['user' => $user->getEmail()]);
    
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
    public function login(Request $request, UserPasswordHasherInterface $passwordHasher, JWTTokenManagerInterface $jwtManager, UserRepository $repository, RefreshTokenService $refreshTokenService): JsonResponse
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
            'authToken' => $token,
            'refreshToken' => $refreshToken,
            'expiresIn' => 3600,
            'email' => $data['email']
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
