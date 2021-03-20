<?php

namespace App\Service;

use App\Model\WikiItem;
use http\Exception\RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

class Extractor {

    private $cacheStructureFile, $sourceDir;

    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface  $serializer, string $sourceDir, string $cacheStructureFile)
    {
        $this->serializer = $serializer;
        $this->cacheStructureFile = $cacheStructureFile;
        $this->sourceDir = $sourceDir;

        $dir = dirname($this->cacheStructureFile);

        if(!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new \RuntimeException(error_get_last()['message'], error_get_last()['type']);
        }
    }

    public function extract(): void
    {
        $directoryParser = new DirectoryParser($this->sourceDir);

        $data = $directoryParser->parse();

        $json = $this->serializer->serialize($data, 'json');

        if (false === file_put_contents($this->cacheStructureFile, $json)) {
            throw new \RuntimeException(error_get_last()['message'], error_get_last()['type']);
        }
    }


}

