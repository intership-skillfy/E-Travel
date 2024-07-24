<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RefreshTokenService
{
    private $jwtEncoder;
    private $ttl;

    public function __construct(JWTEncoderInterface $jwtEncoder, int $ttl)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->ttl = $ttl;
    }

    public function createRefreshToken(UserInterface $user): string
    {
        $payload = [
            'email' => $user->getEmail(),
            'exp' => time() + $this->ttl
        ];

        return $this->jwtEncoder->encode($payload);
    }

    public function decodeRefreshToken(string $refreshToken): ?array
    {
        try {
            return $this->jwtEncoder->decode($refreshToken);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function isRefreshTokenValid(array $payload): bool
    {
        return isset($payload['exp']) && $payload['exp'] > time();
    }
}
