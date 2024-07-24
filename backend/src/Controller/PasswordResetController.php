<?php

namespace App\Controller;

use App\Entity\PasswordResetRequest;
use App\Repository\UserRepository;
use App\Repository\PasswordResetRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use OpenApi\Attributes as OA;

class PasswordResetController extends AbstractController
{
    #[Route('/api/reset-password', name: 'app_password_reset_request', methods: ['POST'])]
    #[OA\Post(
        path: "/api/reset-password",
        tags: ["Reset Password"],
        summary: "asking for reset password link",

    )]
    #[OA\RequestBody(
        required: true,
        description: "User email",
        content: new OA\JsonContent(
            type: "object",
            example: [
                "email" => "test@test.com",
            ]

        ),
    )]
    public function request(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, UserRepository $userRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['message' => 'Email is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $resetRequest = new PasswordResetRequest();
            $resetRequest->setEmail($email);
            $resetRequest->setToken($token);
            $resetRequest->setExpiresAt(new \DateTimeImmutable('+1 hour'));

            $entityManager->persist($resetRequest);
            $entityManager->flush();

            $resetUrl = $this->generateUrl('app_password_reset', ['token' => $token], true);
            $email = (new Email())
                ->from('no-reply@example.com')
                ->to($email)
                ->subject('Password Reset Request')
                ->html("<p>To reset your password, please click the following link: <a href=\"$resetUrl\">$resetUrl</a></p>");

            $mailer->send($email);
        }

        return new JsonResponse(['message' => 'If the email address is found, a password reset link will be sent.'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/reset-password/{token}', name: 'app_password_reset', methods: ['POST'])]
    #[OA\Post(
        path: "/api/reset-password/{token}",
        tags: ["Reset Password"],
        summary: "reset password",

    )]
    #[OA\RequestBody(
        required: true,
        description: "User email",
        content: new OA\JsonContent(
            type: "object",
            example: [
                "password" => "newpassword",
            ]

        ),
    )]
    public function reset(Request $request, string $token, PasswordResetRequestRepository $resetRequestRepository, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): JsonResponse
    {
        $resetRequest = $resetRequestRepository->findOneBy(['token' => $token]);

        if (!$resetRequest || $resetRequest->getExpiresAt() < new \DateTime()) {
            return new JsonResponse(['message' => 'Invalid or expired password reset token.'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);
        $newPassword = $data['password'] ?? null;

        if (!$newPassword) {
            return new JsonResponse(['message' => 'Password is required.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $resetRequest->getEmail()]);

        if ($user) {
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));

            $entityManager->persist($user);
            $entityManager->remove($resetRequest);
            $entityManager->flush();

            return new JsonResponse(['message' => 'Your password has been successfully reset.'], JsonResponse::HTTP_OK);
        }

        return new JsonResponse(['message' => 'User not found.'], JsonResponse::HTTP_NOT_FOUND);
    }
}
