<?php

namespace App\Model;

class WikiItem
{
    private $type;

    private $path;

    private $name;

    /** @var WikiItem[] */
    private $childs;

    public function __construct($type, $path, $name, array $childs = []) {
        $this->path = $path;
        $this->type = $type;
        $this->name = $name;
        $this->childs = $childs;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return WikiItem[]
     */
    public function getChilds(): array
    {
        return $this->childs;
    }
}