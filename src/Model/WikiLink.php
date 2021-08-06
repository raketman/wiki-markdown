<?php

namespace App\Model;

class WikiLink
{
    private $title;

    private $code;

    public function __construct($title, $code)
    {
        $this->title = $title;
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
}
