<?php

namespace App\Helper;

use App\Enum\WikiType;
use App\Model\WikiItem;
use App\Model\WikiLink;
use App\Model\WikiOption;
use Symfony\Component\Yaml\Yaml;

class DirectoryParser {
    private $wikiDir;

    /** @var FileParser  */
    private $fileParser;

    public function __construct(string $wikiDir)
    {
        $this->wikiDir = $wikiDir;

        $this->fileParser = new FileParser();
    }

    public function parse(): WikiItem
    {
        $childs = $this->readDir($this->wikiDir);

        $meta = $this->getDataFromMeta($this->wikiDir);

        $item = new WikiItem(
            WikiType::DIR,
            $this->getRelativePath($this->wikiDir),
            $meta['title'],
            0,
            (new WikiOption())->setExtension(WikiType::DIR),
            $childs);

        return $this->orderWikiDir($item, $meta['order']);
    }

    public function getLastChangeTime($lastTime, $dir = null)
    {
        $dir = $dir ? : $this->wikiDir;

        $iterator = new \DirectoryIterator($dir);

        while ($iterator->valid()) {
            $item = $iterator->current();

            if ($item->isDot()) {
                $iterator->next();
                continue;
            }

            // если картинка, то пропустим (либо отдельной опцией)

            if ($item->isDir()) {
                $lastTime = \max($this->getLastChangeTime($lastTime, $item->getPathname()), $lastTime);
            } else {
                $lastTime = \max($iterator->getMTime(), $lastTime);
            }

            $iterator->next();
        }


        return $lastTime;
    }


    private function readDir($dir): array
    {
        $result = [];

        $iterator = new \DirectoryIterator($dir);

        $counter = 0;
        while ($iterator->valid()) {
            $item = $iterator->current();

            if ($item->isDot() || $item->getBasename() == '.meta' || $this->isIgnoreFiles($item)) {
                $iterator->next();
                continue;
            }

            $path = $this->getRelativePath($item->getPathname());

            if ($this->isResource($item)) {
                $result[] = new WikiItem(
                    WikiType::RESOURCE,
                    $path,
                    $path,
                    $counter++,
                    (new WikiOption())->setExtension(pathinfo($item->getPathname(), PATHINFO_EXTENSION))
                );
            }elseif ($item->isDir()) {


                $meta = $this->getDataFromMeta($item->getPathname());
                $dirItem = new WikiItem(
                    WikiType::DIR,
                    $path,
                    $meta['title'],
                    $counter++,
                    (new WikiOption())->setExtension(WikiType::DIR),
                    $this->readDir($item->getPathname())
                );

                $dirItem = $this->orderWikiDir($dirItem, $meta['order']);

                $result[] = $dirItem;
            } else {
                $result[] = new WikiItem(
                    WikiType::FILE,
                    $path,
                    $this->getNameFromMarkdown($item->getPathname()),
                    $counter++,
                    (new WikiOption())
                        ->setExtension(pathinfo($item->getPathname(), PATHINFO_EXTENSION))
                        ->setLinks($this->getLinksFromMarkdown($path, $item->getPathname()))
                );
            }

            $iterator->next();
        }

        return $result;
    }


    private function orderWikiDir(WikiItem $item, $order)
    {
        if (empty($order)) {
            return $item;
        }


        $childs = $item->getChilds();
        foreach ($childs as $child) {
            $checkKey = mb_substr($child->getPath(), 1);
            if (isset($order[$checkKey])) {
                $child->setOrder($order[$checkKey]);
            }
        }

        usort($childs, function(WikiItem $a, WikiItem  $b) {
            return $a->getOrder() > $b->getOrder();
        });

        $item->setChilds($childs);

        return $item;
    }

    /**
     * @param \SplFileInfo $item
     * @return bool
     */
    private function isResource($item) {
        // TODO application/json?!
        return !$item->isDir() && 0 !== \strpos(\mime_content_type ($item->getPathname()), 'text/');
    }

    private function getDataFromMeta($dir)
    {
        $metaFile = $dir . '/.meta';
        if (!\file_exists($metaFile)) {
            $paths = \explode('/', $dir);
            return ['title' => \end($paths), 'order' => []];
        }

        return array_merge(['title' => $dir, 'order' => []], (array)Yaml::parse(file_get_contents($metaFile)));
    }

    private function getNameFromMarkdown($file)
    {
        // TODO: первую где есть #
        $res = \fopen($file, 'r');

        $name = \fgets($res);
        \fclose($res);

        return  $this->fileParser->clean($name);
    }


    private function getLinksFromMarkdown($url, $file)
    {
        $links = [];

        $this->fileParser->linkParser($url, $file, function ($str, $link) use(&$links) {
            if ($link) {
                $links[] = $link;
            }
        });

        return $links;
    }


    private function getRelativePath($path)
    {
        return \str_replace($this->wikiDir, '', $path);
    }


    private function isIgnoreFiles(\DirectoryIterator $filename)
    {
        $firstSymbol = mb_substr($filename->getBasename(), 0, 1);


        if ($filename->isDir()) {
            if (in_array($firstSymbol, ['.'])) {
                return true;
            }
            return false;
        }
        
        if (in_array($firstSymbol, ['_', '.'])) {
            return true;
        }
        
        return strpos($filename->getPathname(), '.') < 1
            || $filename->getExtension() === 'html';
    }
}

