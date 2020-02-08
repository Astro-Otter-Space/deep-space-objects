<?php

namespace App\Entity\SocialNetworks;

/**
 * Class FacebookPost
 * @package App\Entity\DTO
 */
final class FacebookPost
{
    /** @var  */
    private $id;

    /** @var  */
    private $actions;

    /** @var  */
    private $caption;

    /** @var  */
    private $createdTime;

    /** @var  */
    private $updatedTime;

    /** @var  */
    private $fullPicture;

    /** @var  */
    private $icon;

    /** @var  */
    private $isPopular;

    /** @var  */
    private $link;

    /** @var  */
    private $message;

    /** @var  */
    private $name;

    /** @var  */
    private $permalinkUrl;

    /** @var  */
    private $type;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return FacebookPost
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param mixed $actions
     *
     * @return FacebookPost
     */
    public function setActions($actions): self
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * @param mixed $caption
     *
     * @return FacebookPost
     */
    public function setCaption($caption): self
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * @param mixed $createdTime
     *
     * @return FacebookPost
     */
    public function setCreatedTime($createdTime): self
    {
        $this->createdTime = $createdTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedTime()
    {
        return $this->updatedTime;
    }

    /**
     * @param mixed $updatedTime
     *
     * @return FacebookPost
     */
    public function setUpdatedTime($updatedTime): self
    {
        $this->updatedTime = $updatedTime;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFullPicture()
    {
        return $this->fullPicture;
    }

    /**
     * @param mixed $fullPicture
     *
     * @return FacebookPost
     */
    public function setFullPicture($fullPicture): self
    {
        $this->fullPicture = $fullPicture;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed $icon
     *
     * @return FacebookPost
     */
    public function setIcon($icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsPopular()
    {
        return $this->isPopular;
    }

    /**
     * @param mixed $isPopular
     *
     * @return FacebookPost
     */
    public function setIsPopular($isPopular): self
    {
        $this->isPopular = $isPopular;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param mixed $link
     *
     * @return FacebookPost
     */
    public function setLink($link): self
    {
        $this->link = $link;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     *
     * @return FacebookPost
     */
    public function setMessage($message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     *
     * @return FacebookPost
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermalinkUrl()
    {
        return $this->permalinkUrl;
    }

    /**
     * @param mixed $permalinkUrl
     *
     * @return FacebookPost
     */
    public function setPermalinkUrl($permalinkUrl): self
    {
        $this->permalinkUrl = $permalinkUrl;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     *
     * @return FacebookPost
     */
    public function setType($type): self
    {
        $this->type = $type;
        return $this;
    }

}
