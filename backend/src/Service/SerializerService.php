<?php 

namespace App\Service;

use App\Entity\Offre;
use App\Entity\Category;
use App\Entity\Agent;
use App\Entity\Excursion;
use App\Entity\Hiking;

class SerializerService
{
    public function serialize($entity): array
    {
        if ($entity instanceof Offre) {
            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'description' => $entity->getDescription(),
                'detailedDescription' => $entity->getDetailedDescription(),
                'images' => $entity->getImages(),
                'startDate' => $entity->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $entity->getEndDate()->format('Y-m-d H:i:s'),
                'destination' => $entity->getDestination(),
                'category' => $entity->getCategory() ? $entity->getCategory()->getName() : null,
                'tarifs' => $this->serializeArray($entity->getTarifs()->toArray()),
            ];
        }
        
        if ($entity instanceof Category) {
            $offersArray = [];
            foreach ($entity->getOffres() as $offer) {
                $offersArray[] = [
                    'id' => $offer->getId(),
                    'name' => $offer->getName(),
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
                'agency' => $entity->getAgency() ? $entity->getAgency()->getName() : null,
            ];
        }

        if ($entity instanceof Excursion) {
            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'description' => $entity->getDescription(),
                'detailedDescription' => $entity->getDetailedDescription(),
                'images' => $entity->getImages(),
                'startDate' => $entity->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $entity->getEndDate()->format('Y-m-d H:i:s'),
                'destination' => $entity->getDestination(),
                'category' => $entity->getCategory() ? $entity->getCategory()->getName() : null,
                'tarifs' => $this->serializeArray($entity->getTarifs()->toArray()),
                'included' => $entity->isIncluded(),
            ];
        }

        if ($entity instanceof Hiking) {
            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'description' => $entity->getDescription(),
                'detailedDescription' => $entity->getDetailedDescription(),
                'images' => $entity->getImages(),
                'startDate' => $entity->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $entity->getEndDate()->format('Y-m-d H:i:s'),
                'destination' => $entity->getDestination(),
                'category' => $entity->getCategory() ? $entity->getCategory()->getName() : null,
                'tarifs' => $this->serializeArray($entity->getTarifs()->toArray()),
                'difficulty' => $entity->getDifficulty(),
            ];
        }

        if ($entity instanceof Omra) {
            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'description' => $entity->getDescription(),
                'detailedDescription' => $entity->getDetailedDescription(),
                'images' => $entity->getImages(),
                'startDate' => $entity->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $entity->getEndDate()->format('Y-m-d H:i:s'),
                'destination' => $entity->getDestination(),
                'category' => $entity->getCategory() ? $entity->getCategory()->getName() : null,
                'tarifs' => $this->serializeArray($entity->getTarifs()->toArray()),
            ];
        }
        if ($entity instanceof Trip) {
            return [
                'id' => $entity->getId(),
                'name' => $entity->getName(),
                'description' => $entity->getDescription(),
                'detailedDescription' => $entity->getDetailedDescription(),
                'images' => $entity->getImages(),
                'startDate' => $entity->getStartDate()->format('Y-m-d H:i:s'),
                'endDate' => $entity->getEndDate()->format('Y-m-d H:i:s'),
                'destination' => $entity->getDestination(),
                'category' => $entity->getCategory() ? $entity->getCategory()->getName() : null,
                'tarifs' => $this->serializeArray($entity->getTarifs()->toArray()),
            ];
        }

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
