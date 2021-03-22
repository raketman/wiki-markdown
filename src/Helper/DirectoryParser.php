<?php

namespace App\Helper;

use App\Enum\WikiType;
use App\Model\WikiItem;

class DirectoryParser {
    private $wikiDir;

    public function __construct(string $wikiDir)
    {
        $this->wikiDir = $wikiDir;
    }

    public function parse(): WikiItem
    {
        $childs = $this->readDir($this->wikiDir);

        return new WikiItem(WikiType::DIR, $this->getRelativePath($this->wikiDir), $this->getNameFromMeta($this->wikiDir), $childs);
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

            if ($item->isDir()) {
                $result[] = new WikiItem(
                    WikiType::DIR,
                    $path,
                    $this->getNameFromMeta($item->getPathname()),
                    $this->readDir($item->getPathname())
                );
            } else {
                $result[] = new WikiItem(
                    WikiType::FILE,
                    $path,
                    $this->getNameFromMarkdown($item->getPathname())
                );
            }

            $iterator->next();
        }

        return $result;
    }

    private function getNameFromMeta($dir)
    {
        $res = fopen($dir . '/.meta', 'r');

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

