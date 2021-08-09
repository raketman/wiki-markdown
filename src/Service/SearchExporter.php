<?php

namespace App\Service;

use App\Enum\SearchIndex;
use App\Enum\WikiType;
use App\Helper\DirectoryParser;
use App\Model\WikiItem;
use MeiliSearch\Endpoints\Indexes;
use MeiliSearch\Exceptions\ApiException;
use \RuntimeException;
use MeiliSearch\Client;

final class SearchExporter {

    private $sourceDir;

    /** @var Client  */
    private $meili;

    /** @var Extractor */
    private $extractor;

    public function __construct(Client $meili, Extractor $extractor, string $sourceDir)
    {
        $this->sourceDir = $sourceDir;
        $this->meili = $meili;
        $this->extractor = $extractor;
    }

    public function export()
    {
        $directoryParser = new DirectoryParser($this->sourceDir);

        $data = $directoryParser->parse();

        try {
            $index = $this->meili->getIndex(SearchIndex::PAGE);
            $index->delete();
        } catch (\Throwable $e) {
        } finally {
            $index = $this->meili->createIndex(SearchIndex::PAGE);
        }

        $documents = $this->getDocuments($data);
        $index->addDocuments($documents);
    }

    public function search($query)
    {
        if (strlen($query) === 0) {
            return [];
        }

        return $this->meili->index(SearchIndex::PAGE)->search($query)->getHits();
    }

    private function getDocuments(WikiItem $item)
    {
        $result = [];

        if ($item->getType() === WikiType::FILE) {
            $result[] = [
                'search_id' => md5($item->getId()),
                'id'        => $item->getId(),
                'content'   => $this->extractor->getPageContent($item->getPath()),
                'name'      => $item->getName()
            ];
        }

        foreach ($item->getChilds() as $child) {
            $result = array_merge($result, $this->getDocuments($child));
        }

        return $result;
    }

}

