<?php

namespace App\Model;

class WikiOption
{
    private $extension;

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @param mixed $extension
     * @return WikiOption
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;
        return $this;
    }

}