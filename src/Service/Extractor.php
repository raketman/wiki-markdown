<?php

namespace App\Service;

use App\Helper\DirectoryParser;
use \RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

final class Extractor {

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

    public function hasChange(): bool
    {
        if (false === file_exists($this->cacheStructureFile)) {
            return true;
        }

        $time = filemtime ($this->cacheStructureFile);


        $dirChangeTime = (new DirectoryParser($this->sourceDir))->getLastChangeTime($time);



        return $time < $dirChangeTime;
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

    public function getListContent()
    {
        if (!file_exists($this->cacheStructureFile)) {
            $this->extractor->extract();
        }

        return file_get_contents($this->cacheStructureFile);
    }

    public function getPageContent($page)
    {
        $pagePath = sprintf('%s%s',$this->sourceDir, $page);

        if (!file_exists($pagePath)) {
            throw new \RuntimeException("Не найдена страница", 500);
        } else {
            return file_get_contents($pagePath);
        }

    }


}

