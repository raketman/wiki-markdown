<?php

namespace App\Helper;

use App\Enum\WikiType;
use App\Model\WikiItem;
use App\Model\WikiOption;

class DirectoryParser {
    private $wikiDir;

    public function __construct(string $wikiDir)
    {
        $this->wikiDir = $wikiDir;
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
                $lastTime = max($this->getLastChangeTime($lastTime, $item->getPathname()), $lastTime);
            } else {
                $lastTime = max($iterator->getMTime(), $lastTime);
            }

            $iterator->next();
        }


        return $lastTime;
    }


    private function readDir($dir)
    {
        $result = [];

        $iterator = new \DirectoryIterator($dir);

        while ($iterator->valid()) {
            $item = $iterator->current();

            if ($item->isDot() || $item->getBasename() == '.meta') {
                $iterator->next();
                continue;
            }

            $path = $this->getRelativePath($item->getPathname());

            if (!$item->isDir() && 0 !== strpos(mime_content_type ($item->getPathname()), 'text/')) {
                $iterator->next();
                continue;
            }

            if ($item->isDir()) {
                $childs = $this->readDir($item->getPathname());

                if ($childs) {
                    $result[] = new WikiItem(
                        WikiType::DIR,
                        $path,
                        $this->getNameFromMeta($item->getPathname()),
                        (new WikiOption())->setExtension(WikiType::DIR),
                        $childs
                    );
                }
            } else {
                $result[] = new WikiItem(
                    WikiType::FILE,
                    $path,
                    $this->getNameFromMarkdown($item->getPathname()),
                    (new WikiOption())->setExtension(pathinfo($item->getPathname(), PATHINFO_EXTENSION))
                );
            }

            $iterator->next();
        }

        return $result;
    }

    private function getNameFromMeta($dir)
    {
        $metaFile = $dir . '/.meta';
        if (!file_exists($metaFile)) {
            $paths = explode('/', $dir);
            return end($paths);
        }

        $res = fopen($metaFile, 'r');

        $name = fgets($res);
        fclose($res);

        return $this->clean(str_replace('#', '', $name));
    }

    private function getNameFromMarkdown($file)
    {
        $res = fopen($file, 'r');

        $name = fgets($res);
        fclose($res);

        return $this->clean(str_replace('#', '', $name));
    }


    private function getRelativePath($path)
    {
        return str_replace($this->wikiDir, '', $path);
    }

    private function clean($str)
    {
        return str_replace([PHP_EOL, "\r\n"], "", $str);
    }
}

