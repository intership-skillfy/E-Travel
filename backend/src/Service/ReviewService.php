<?php

namespace App\Service;
use Psr\Log\LoggerInterface;


use App\Entity\Client;
use App\Entity\Offre;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;

class ReviewService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createReview(array $data, Client $client, Offre $offre): Review
    {
        $review = new Review();
        $review->setComment($data['comment']);
        $review->setRating($data['rating']);
        $review->setClient($client);
        $review->setOffre($offre);

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $review;
    }

    public function updateReview(Review $review, array $data, Client $client, Offre $offre): Review
    {
        $review->setComment($data['comment']);
        $review->setRating($data['rating']);
        $review->setClient($client);
        $review->setOffre($offre);

        $this->entityManager->flush();

        return $review;
    }

    public function deleteReview(Review $review): void
    {
        $this->entityManager->remove($review);
        $this->entityManager->flush();
    }

    public function transformReview(Review $review): array
    {
        
        return [
            'id' => $review->getId(),
            'comment' => $review->getComment(),
            'rating' => $review->getRating(),
            'createdAt' => $review->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
