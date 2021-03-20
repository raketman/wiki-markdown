<?php

namespace App\Service;

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

        return new WikiItem(WikiType::DIR, $this->wikiDir, $this->getNameFromMeta($this->wikiDir), $childs);
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

            if ($item->isDir()) {
                $result[] = new WikiItem(
                    WikiType::DIR,
                    $item->getPathname(),
                    $this->getNameFromMeta($item->getPathname()),
                    $this->readDir($item->getPathname())
                );
            } else {
                $result[] = new WikiItem(
                    WikiType::FILE,
                    $item->getPathname(),
                    $this->getNameFromMarkdown($item->getPathname())
                );
            }

            $iterator->next();
        }

        return $result;
    }

    private function getNameFromMeta($dir)
    {
        return file_get_contents($dir . '/.meta');
    }

    private function getNameFromMarkdown($file)
    {
        $res = fopen($file, 'r');

        $name = fgets($res);
        fclose($res);

        return str_replace('#', '', $name);
    }
}

