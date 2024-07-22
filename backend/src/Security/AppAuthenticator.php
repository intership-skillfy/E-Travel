<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AppAuthenticator extends AbstractAuthenticator
{
    private $jwtManager;
    private $userProvider;
    private $jwtEncoder;
    private $logger;i


    public function __construct(JWTTokenManagerInterface $jwtManager, UserProviderInterface $userProvider, JWTEncoderInterface $jwtEncoder, LoggerInterface $logger)
    {
        $this->jwtManager = $jwtManager;
        $this->userProvider = $userProvider;
        $this->jwtEncoder = $jwtEncoder;
        $this->logger = $logger;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') && str_contains($request->headers->get('Authorization'), 'Bearer');
    }

    public function authenticate(Request $request): Passport
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $authorizationHeader);
        
        try {
            $decodedToken = $this->jwtEncoder->decode($token);
            $this->logger->info('Decoded JWT token', ['decodedToken' => $decodedToken]);

            if (!$decodedToken || !isset($decodedToken['username'])) {
                throw new AuthenticationException('Invalid JWT token: Missing username');
            }

            return new SelfValidatingPassport(
                new UserBadge($decodedToken['username'], function ($userIdentifier) {
                    return $this->userProvider->loadUserByIdentifier($userIdentifier);
                })
            );
        } catch (\Exception $e) {
            $this->logger->error('JWT token decoding failed', ['exception' => $e]);
            throw new AuthenticationException('Invalid JWT token: ' . $e->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'message' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
