<?php

namespace App\Controller;

use App\Entity\History;
use App\Entity\Client;
use App\Repository\HistoryRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;

#[Route('/api/history')]

class HistoryController extends AbstractController
{
    #[Route('/', name:'get_histories', methods:['GET'])]
    #[OA\Tag(name: 'History')]
    #[OA\Response(
        response: 200,
        description: 'Returns list of histories',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: History::class, groups: ['full']))
        )
    )]
   
    public function getHistories(HistoryRepository $historyRepository): JsonResponse
{
    $histories = $historyRepository->findAll();

    if (!$histories) {
        return $this->json(['error' => 'Histories not found'], 404);
    }

    $histories_array = [];

    foreach ($histories as $history) {
        $client_array=[];
        foreach ($history->getClient() as $client){
            $client_array[]=[
                'id'=>$client->getId(),
                'name'=>$client->getClient(),
                'phone'=>$client->getPhone()
            ];
            
        }


        $reservations_array = [];
        
        foreach ($history->getReservation() as $reservation) {
            $reservations_array[] = [
                'id' => $reservation->getId(),
                'status' => $reservation->getStatus(),
                'amount' => $reservation->getAmount()
            ];
        }

        $histories_array[] = [
            'id' => $history->getId(),
            'search_history' => $history->getSearchHistory(),
            'client' => $client_array,
            'reservation' => $reservations_array
        ];
    }

    return new JsonResponse($histories_array);
}




#[Route('/{id}', name:'get_history', methods:['GET'])]
#[OA\Tag(name: 'History')]
    #[OA\Response(
        response: 200,
        description: 'Returns history',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: History::class, groups: ['full']))
        )
    )]

public function getHistory(int $id, HistoryRepository $historyRepository): JsonResponse
{
    $history = $historyRepository->find($id);

    if (!$history) {
        return $this->json(['error' => 'History not found'], 404);
    }

    $reservations_array = [];
    foreach ($history->getReservation() as $reservation) {
        $reservations_array[] = [
            'id' => $reservation->getId(),
            'status' => $reservation->getStatus(),
            'amount' => $reservation->getAmount()
        ];
    }

    $client_array=[];
        foreach ($history->getClient() as $client){
            $client_array[]=[
                'id'=>$client->getId(),
                'name'=>$client->getClient(),
                'phone'=>$client->getPhone()
            ];
            
        }
    $history_array = [
        'id' => $history->getId(),
        'search_history' => $history->getSearchHistory(),
        'client' => $client_array,
        'reservation' => $reservations_array
    ];

    return new JsonResponse($history_array);
}




    #[Route('/client/{clientId}', name: 'client_history', methods: ["GET"])]
    #[OA\Tag(name: 'History')]
    #[OA\Response(
        response: 200,
        description: 'Returns history by client' ,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: History::class, groups: ['full']))
        )
    )]
    public function getClientHistory(int $clientId, ClientRepository $clientRepository): JsonResponse
    {
        $client = $clientRepository->find($clientId);

        if (!$client) {
            return $this->json(['error' => 'Client not found'], 404);
        }

        $history = $client->getHistory();  
        

        $history_array = [];

            $reservations_array = [];
            foreach ($history->getReservation() as $reservation) {
                $reservations_array[] = [
                    'id' => $reservation->getId(),
                    'status' => $reservation->getStatus(),
                    'amount' => $reservation->getAmount(),
                ];
            }

            $history_array[] = [
                'id' => $history->getId(),
                'search_history' => $history->getSearchHistory(),
                'reservations' => $reservations_array,
            ];
        

        return new JsonResponse($history_array);
    }



    
   
}
