<?php 
namespace App\Service;

use App\Entity\Offer;
use App\Entity\Category;
use App\Entity\Agent;

class SerializerService
{
    public function serialize($entity): array
    {
        if ($entity instanceof Offer) {
            return [
                'id' => $entity->getId(),
                'title' => $entity->getTitle(),
                'category' => $entity->getCategory()->getName(),
                'description' => $entity->getDescription(),
                'startDate' => $entity->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $entity->getEndDate()->format('Y-m-d H:i:s'),
                'price' => $entity->getPrice(),
                'destination' => $entity->getDestination(),
                'updatedAt' => $entity->getUpdatedAt()->format('Y-m-d H:i:s'),
                'capacity' => $entity->getCapacity(),
            ];
        }
        if ($entity instanceof Category) {
            $offersArray = [];

            foreach ($entity->getOffers() as $offer) {
                $offersArray[] = [
                    'id' => $offer->getId(),
                    'title' => $offer->getTitle(),
                ];
            }

            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'offers' => $offersArray,
            ];
        }

        if ($entity instanceof Agent) {
            return [
                'id' => $entity->getId(),
                'email' => $entity->getEmail(),
                'name' => $entity->getName(),
                'phone' => $entity->getPhone(),
                'roles' => $entity->getRoles(),
                'agency' => $entity->getAgency()->getName(),
            ];
        }

        // Add more entities as needed

        return [];
    }

    public function serializeArray(array $entities): array
    {
        $serializedArray = [];
        foreach ($entities as $entity) {
            $serializedArray[] = $this->serialize($entity);
        }
        return $serializedArray;
    }
}
