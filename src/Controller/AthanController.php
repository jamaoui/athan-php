<?php

namespace App\Controller;

use App\Service\ApiService;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AthanController extends AbstractController
{
    private ApiService $apiService;
    private ?Request $request;

    public function __construct(ApiService $apiService, RequestStack $requestStack)
    {
        $this->apiService = $apiService;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @Route("/api/get", name="get_api")
     * @return Response
     * @throws JsonException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getApi(): Response
    {
        $longitude = $this->request->get('longitude');
        $latitude = $this->request->get('latitude');
        $method = $this->request->get('method', 4);
        $errors = [];
        $dataContent = [];
        $code = 200;

        if ($latitude === null) {
            $errors[] = (new  ParameterNotFoundException('latitude'))->getMessage();
        }
        if ($longitude === null) {
            $errors[] = (new  ParameterNotFoundException('longitude'))->getMessage();
        }
        if (empty($errors)) {
            $year = $this->request->get('year', date('Y'));
            $month = $this->request->get('month', date('m'));
            $day = $this->request->get('day');
            $response = $this->apiService->getData($method, $longitude, $latitude, $year, $month);
            $content = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $code = $content['code'];
            $dataContent = $content['data'];
            if ($day !== null) {
                $dayKey = $day - 1;
                if (array_key_exists($dayKey, $dataContent)) {
                    $dataContent = $dataContent[$dayKey];
                } else {
                    $dataContent = [];
                }
            }

        }

        return $this->json(['data' => $dataContent, 'errors' => $errors], $code);
    }

    /**
     * @Route("/", name="athan")
     */
    public function index(): Response
    {
        return $this->render('athan/index.html.twig', []);
    }
}
