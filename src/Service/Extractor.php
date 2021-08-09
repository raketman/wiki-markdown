<?php

namespace App\Service;

use App\Enum\WikiType;
use App\Helper\DirectoryParser;
use App\Helper\UrlCreator;
use App\Model\WikiItem;
use App\Model\WikiLink;
use \RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

final class Extractor {

    private $cacheStructureFile, $sourceDir, $publicDir;

    /** @var SerializerInterface */
    private $serializer;

    /** @var UrlCreator  */
    private $urlCreator;


    public function __construct(SerializerInterface  $serializer, string $sourceDir, string $cacheStructureFile, string $publicDir)
    {
        $this->serializer = $serializer;
        $this->cacheStructureFile = $cacheStructureFile;
        $this->sourceDir = $sourceDir;
        $this->publicDir = $publicDir;

        $dir = \dirname($this->cacheStructureFile);

        if(!\is_dir($dir) && !\mkdir($dir, 0755, true) && !\is_dir($dir)) {
            throw new \RuntimeException(\error_get_last()['message'], \error_get_last()['type']);
        }

        $this->urlCreator = new UrlCreator();
    }

    public function hasChange(): bool
    {
        if (false === \file_exists($this->cacheStructureFile)) {
            return true;
        }

        $time = \filemtime ($this->cacheStructureFile);


        $dirChangeTime = (new DirectoryParser($this->sourceDir))->getLastChangeTime($time);



        return $time < $dirChangeTime;
    }


    public function extract(): void
    {
        $directoryParser = new DirectoryParser($this->sourceDir);

        $data = $directoryParser->parse();

        $this->copyWiki($data);
        $data = $this->cleanWiki($data);

        $json = $this->serializer->serialize($data, 'json');

        if (false === \file_put_contents($this->cacheStructureFile, $json)) {
            throw new \RuntimeException(\error_get_last()['message'], \error_get_last()['type']);
        }
    }

    public function getListContent()
    {
        if (!\file_exists($this->cacheStructureFile)) {
            $this->extract();
        }

        return file_get_contents($this->cacheStructureFile);
    }

    public function getPageContent($page)
    {
        $pagePath = \sprintf('%s%s',$this->sourceDir, $page);

        if (!\file_exists($pagePath)) {
            throw new \RuntimeException("Не найдена страница", 500);
        } else {
            $content = [];

            // Добавим ссылки
            $res = \fopen($pagePath, 'r');

            // Пропуска название
            $content[] = \fgets($res);

            while(!\feof($res)) {
                $str =  \trim(\fgets($res));

                if (\strpos($str, '#') === 0) {
                    $link = new WikiLink($this->urlCreator->clean($str),  (new UrlCreator())->createHashUrl(\str_replace('\\', '/', $page), $this->urlCreator->clean($str)));
                    $counter = 0;
                    $symbol = \substr($str, $counter, 1);
                    while($symbol === '#') {
                        $symbol = \substr($str, ++$counter, 1);
                    }
                    $prefix = \str_pad('',$counter, '#');
                    $content[] = \sprintf('%s <a id="%s"></a>%s %s', $prefix, $link->getCode(), $link->getTitle(), $prefix);
                } else {
                    $content[] = $str;
                }
            }
            \fclose($res);

            return \implode(PHP_EOL, $content);
        }

    }


    private function copyWiki(WikiItem $wikiItem): void
    {
        foreach ($wikiItem->getChilds() as $child) {
            if ($child->getType() === WikiType::DIR) {
                $this->copyWiki($child);
                continue;
            }
            if ($child->getType() !== WikiType::RESOURCE) {
                continue;
            }
            $sourceFile = $this->sourceDir . $child->getPath();
            $distFile = $this->publicDir . $child->getPath();
            if (!file_exists(dirname($distFile)) && false === \mkdir(dirname($distFile), 0775, true)) {
                throw new \RuntimeException(error_get_last()['message'], error_get_last()['code']);
            }
            if (!\copy($sourceFile, $distFile)) {
                throw new \RuntimeException(error_get_last()['message'], error_get_last()['code']);
            }
            continue;

        }

    }

    private function cleanWiki(WikiItem $wikiItem): WikiItem
    {
        $newChilds = [];
        foreach ($wikiItem->getChilds() as $child) {
            if ($child->getType() === WikiType::RESOURCE) {
                continue;
            }

            if ($child->getType() === WikiType::DIR && !$this->hasFile($child->getChilds())) {
                continue;
            }

            $child = $this->cleanWiki($child);

            $newChilds[] = $child;
        }

        $wikiItem->setChilds($newChilds);
        return $wikiItem;
    }

    /**
     * @param WikiItem[] $wikiItems
     * @return bool
     */
    private function hasFile( $wikiItems): bool
    {
        foreach ($wikiItems as $child) {
            if ($child->getType() === WikiType::FILE) {
                return true;
            }

            if ($this->hasFile($child->getChilds())) {
                return true;
            }
        }

        return false;
    }
}

