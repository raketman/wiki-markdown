<?php

namespace App\Helper;

use App\Enum\WikiType;
use App\Model\WikiItem;
use App\Model\WikiLink;
use App\Model\WikiOption;
use Symfony\Component\Yaml\Yaml;

class DirectoryParser {
    private $wikiDir;

    /** @var UrlCreator  */
    private $urlCreator;

    public function __construct(string $wikiDir)
    {
        $this->wikiDir = $wikiDir;

        $this->urlCreator = new UrlCreator();
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

            if ($item->isDot() || $item->getBasename() == '.meta') {
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

        return  $this->urlCreator->clean($name);
    }


    private function getLinksFromMarkdown($path, $file)
    {
        $res = \fopen($file, 'r');

        // Пропуска название
        \fgets($res);


        $links = [];

        while(!feof($res)) {
            $str =  \fgets($res);

            if (strpos($str, '#') === 0) {
                $links[] = new WikiLink( $this->urlCreator->clean($str),  $this->urlCreator->createHashUrl(str_replace('\\', '/', $path), $this->urlCreator->clean($str)));
            }
        }
        \fclose($res);

        return $links;
    }


    private function getRelativePath($path)
    {
        return \str_replace($this->wikiDir, '', $path);
    }

}

