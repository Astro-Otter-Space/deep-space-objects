<?php

namespace App\Entity\DTO;

final class ConstellationDsoDTO
{
    private string $id;
    private string $name;
    private string $url;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return ConstellationDsoDTO
     */
    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ConstellationDsoDTO
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return ConstellationDsoDTO
     */
    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }
}
