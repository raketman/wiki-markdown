<?php

namespace App\Model;

class WikiOption
{
    private $extension;

    /** @var WikiLink[] */
    private $links = [];

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
        $this->extension = strtolower($extension);
        return $this;
    }

    /**
     * @return WikiLink[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param WikiLink[] $link
     * @return WikiOption
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;
        return $this;
    }



}
