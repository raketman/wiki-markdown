<?php

namespace App\Controller;

use App\Enum\ContentType;
use App\Service\Extractor;
use App\Service\SearchExporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /** @var Extractor  */
    private $extractor;

    /** @var SearchExporter  */
    private $searchExporter;

    public function __construct(Extractor $extractor, SearchExporter $searchExporter)
    {
        $this->extractor = $extractor;
        $this->searchExporter = $searchExporter;
    }

    /**
     * @param Request $request
     *
     * @Route(methods={"GET"}, path="/list.json")
     */
    public function getList(Request $request)
    {
        return new JsonResponse(
            $this->extractor->getListContent(), Response::HTTP_OK, [], true
        );
    }


    /**
     * @param Request $request
     *
     * @Route(methods={"GET"}, path="/page.json")
     */
    public function getPage(Request $request)
    {
        return new JsonResponse([
            'content'   => $this->extractor->getPageContent( $request->get('page')),
            'type'      => ContentType::MARKDOWN
        ]);
    }

    /**
     * @param Request $request
     *
     * @Route(methods={"GET"}, path="/search.json")
     */
    public function getSearch(Request $request)
    {
        return new JsonResponse($this->searchExporter->search($request->get('query')));
    }
}

