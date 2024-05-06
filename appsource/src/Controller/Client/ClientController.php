<?php
namespace App\Controller\Client;

use App\Application\Handler\Client\CreateClientHandler;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController
{
    protected Security $security;
    protected CreateClientHandler $createClient;

    public function __construct(
        CreateClientHandler $createClient,
        Security $security
    ) {
        $this->createClient = $createClient;
        $this->security = $security;
    }

    #[Route('/api/client', name: 'api_client_dashboard', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $username = $this->security->getUser()->getUserIdentifier();
        $response = [];


        $response[] = [
            'username' => $username,
            'message' => "Hello Client, $username"
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/api/client', name: 'api_client_register', methods: ['POST'])]
    public function registerClientApi(Request $request): JsonResponse
    {
        $userArray = json_decode($request->getContent(), true);

        try {
            $this->createClient->handle(
                [
                    'username' => $userArray['username'],
                    'email'    => $userArray['email'],
                    'password' => $userArray['password']
                ]
            );

        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse('Client created', Response::HTTP_CREATED);
    }

    #[Route('/client', name: 'client_register', methods: ['POST'])]
    public function registerClient(Request $request): JsonResponse
    {
        $userArray = json_decode($request->getContent(), true);

        try {
            $this->createClient->handle(
                [
                    'username' => $userArray['username'],
                    'email'    => $userArray['email'],
                    'password' => $userArray['password']
                ]
            );

        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse('Client created', Response::HTTP_CREATED);
    }

    #[Route('/api/clients', name: 'api_client_create', methods: ['POST'])]
    public function addClient(Request $request): JsonResponse
    {
        $userArray = json_decode($request->getContent(), true);

        try {
            $this->createClient->handle(
                [
                    'username' => $userArray['username'],
                    'email'    => $userArray['email'],
                    'password' => $userArray['password']
                ]
            );

        } catch (Exception $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_ACCEPTABLE);
        }

        return new JsonResponse('Client created', Response::HTTP_CREATED);
    }
}
