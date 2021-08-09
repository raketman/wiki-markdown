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

        return new WikiItem(
            WikiType::DIR,
            $this->getRelativePath($this->wikiDir),
            $this->getNameFromMeta($this->wikiDir),
            (new WikiOption())->setExtension(WikiType::DIR),
            $childs);
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
                    (new WikiOption())->setExtension(pathinfo($item->getPathname(), PATHINFO_EXTENSION))
                );
            }elseif ($item->isDir()) {

                $result[] = new WikiItem(
                    WikiType::DIR,
                    $path,
                    $this->getNameFromMeta($item->getPathname()),
                    (new WikiOption())->setExtension(WikiType::DIR),
                    $this->readDir($item->getPathname())
                );
            } else {
                $result[] = new WikiItem(
                    WikiType::FILE,
                    $path,
                    $this->getNameFromMarkdown($item->getPathname()),
                    (new WikiOption())
                        ->setExtension(pathinfo($item->getPathname(), PATHINFO_EXTENSION))
                        ->setLinks($this->getLinksFromMarkdown($path, $item->getPathname()))
                );
            }

            $iterator->next();
        }

        return $result;
    }

    /**
     * @param \SplFileInfo $item
     * @return bool
     */
    private function isResource($item) {
        // TODO application/json?!
        return !$item->isDir() && 0 !== \strpos(\mime_content_type ($item->getPathname()), 'text/');
    }

    private function getNameFromMeta($dir)
    {
        $metaFile = $dir . '/.meta';
        if (!\file_exists($metaFile)) {
            $paths = \explode('/', $dir);
            return \end($paths);
        }

        $data = Yaml::parse(file_get_contents($metaFile));


        return $data['title'];
    }

    private function getNameFromMarkdown($file)
    {
        // TODO: первую непустую или из файла .meta?!
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
        if ($filename->isDir()) {
            return false;
        }

        return
            0 === strpos($filename->getBasename(), '_')
            || strpos($filename->getPathname(), '.') < 1
            || $filename->getExtension() === 'html';
    }
}

