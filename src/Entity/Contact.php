<?php

namespace App\Entity;

use App\Classes\Utils;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Contact
 * @package App\Entity
 */
class Contact
{

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     */
    private $firstname;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     */
    private $lastname;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     * @Assert\Email(message="contact.constraint.email")
     */
    private $email;

    /**
     * @var
     * @Assert\Country(message="contact.constraint.country")
     */
    private $country;

    /**
     * @var
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     * @Assert\Choice(callback="getValidTopics", message="contact.constraint.topic")
     */
    private $topic;

    /**
     * @var
     * @Assert\Blank(message="contact.constraint.invalid_form")
     */
    private $pot2Miel;

    /**
     * @var
     */
    private $message;

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     */
    public function setCountry($country): void
    {
        $this->country = $country;
    }

    /**
     * @return mixed
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param mixed $topic
     */
    public function setTopic($topic): void
    {
        $this->topic = $topic;
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
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getPot2Miel()
    {
        return $this->pot2Miel;
    }

    /**
     * @param mixed $pot2Miel
     */
    public function setPot2Miel($pot2Miel): void
    {
        $this->pot2Miel = $pot2Miel;
    }


    /**
     * @return array
     */
    public static function getValidTopics()
    {
        return array_keys(Utils::listTopicsContact());
    }
}