<?php


namespace App\Entity\DTO;

/**
 * Class TwitterCard
 * @package App\Entity\DTO
 */
class TwitterCard
{
    private $id;

    private $text;

    private $lang;

    private $createdAt;

    private $place;

    private $replyCount;

    private $retweetCount;

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
     * @return TwitterCard
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     *
     * @return TwitterCard
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     *
     * @return TwitterCard
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     *
     * @return TwitterCard
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param mixed $place
     *
     * @return TwitterCard
     */
    public function setPlace($place)
    {
        $this->place = $place;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getReplyCount()
    {
        return $this->replyCount;
    }

    /**
     * @param mixed $replyCount
     *
     * @return TwitterCard
     */
    public function setReplyCount($replyCount)
    {
        $this->replyCount = $replyCount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRetweetCount()
    {
        return $this->retweetCount;
    }

    /**
     * @param mixed $retweetCount
     *
     * @return TwitterCard
     */
    public function setRetweetCount($retweetCount)
    {
        $this->retweetCount = $retweetCount;
        return $this;
    }


}
