<?php

namespace App\Controller;

use App\Enum\ContentType;
use App\Service\Extractor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{

    protected $extractor;

    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
    }

    /**
     * @param Request $request
     *
     * @Route(methods={"GET"}, path="/list.json")
     */
    public function getList(Request $request)
    {
        $structurePath = $this->getParameter('wiki_cache_structure_file');

        if (!file_exists($structurePath)) {
            $content = $this->extractor->extract();
        } else {
            $content = file_get_contents($structurePath);
        }

        return new JsonResponse($content, Response::HTTP_OK, [], true);
    }


    /**
     * @param Request $request
     *
     * @Route(methods={"GET"}, path="/page.json")
     */
    public function getPage(Request $request)
    {
        $wikiDir = $this->getParameter('wiki_source_dir');

        $pagePath = sprintf('%s%s',$wikiDir, $request->get('page'));

        if (!file_exists($pagePath)) {
            throw new \RuntimeException("Не найдена страница", 500);
        } else {
            $content = file_get_contents($pagePath);
        }

        return new JsonResponse([
            'content'   => $content,
            'type'      => ContentType::MARKDOWN
        ]);
    }
}

