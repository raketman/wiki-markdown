<?php

namespace App\Model;

class WikiItem
{
    private $id;

    private $type;

    private $path;

    private $name;

    private $extension;

    /** @var WikiItem[] */
    private $childs;

    public function __construct($type, $extension, $path, $name, array $childs = []) {
        $this->id = md5($path);
        $this->path = $path;
        $this->type = $type;
        $this->name = $name;
        $this->childs = $childs;
        $this->extension = $extension;
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

}