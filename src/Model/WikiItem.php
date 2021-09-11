<?php

namespace App\Model;

class WikiItem
{
    private $id;

    private $type;

    private $path;

    private $name;

    private $order;

    /** @var WikiOption  */
    private $options;

    /** @var WikiItem[] */
    private $childs;

    public function __construct($type, $path, $name, $order, WikiOption $options , array $childs = []) {
        $this->id = str_replace('\\', '/', $path);
        $this->path = $path;
        $this->type = $type;
        $this->name = $name;
        $this->order = $order;
        $this->childs = $childs;
        $this->options = $options;
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
     * @param WikiItem[] $childs
     * @return WikiItem
     */
    public function setChilds(array $childs): WikiItem
    {
        $this->childs = $childs;
        return $this;
    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return WikiOption
     */
    public function getOptions(): WikiOption
    {
        return $this->options;
    }

    /**
     * @param mixed $order
     */
    public function setOrder($order): void
    {
        $this->order = $order;
    }

    /**
     * @return mixed
     */
    public function getOrder()
    {
        return $this->order;
    }
}
