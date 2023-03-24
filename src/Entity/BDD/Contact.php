<?php

namespace App\Entity\BDD;

use App\Classes\Utils;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Contact
 * @package App\Entity
 */
class Contact
{

    /**
     * @var string
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     */
    private string $firstname;

    /**
     * @var string
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     */
    private string $lastname;

    /**
     * @var string
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     * @Assert\Email(message="contact.constraint.email")
     */
    private string $email;

    /**
     * @var string
     * @Assert\Country(message="contact.constraint.country")
     */
    private string $country;

    /**
     * @var string
     * @Assert\NotBlank(message="contact.constraint.not_blank")
     * @Assert\Choice(callback="getValidTopics", message="contact.constraint.topic")
     */
    private string $topic;

    /**
     * @var string
     * @Assert\Blank(message="contact.constraint.invalid_form")
     */
    private string $pot2Miel;

    /**
     * @var string
     */
    private string $message;

    /**
     * @var string
     */
    private string $labelCountry;

    /**
     * @return mixed
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     *
     * @return Contact
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     *
     * @return Contact
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     *
     * @return Contact
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param mixed $country
     *
     * @return Contact
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * @param mixed $topic
     *
     * @return Contact
     */
    public function setTopic(mixed $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     *
     * @return Contact
     */
    public function setMessage(mixed $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getPot2Miel(): string
    {
        return $this->pot2Miel;
    }

    /**
     * @param mixed $pot2Miel
     */
    public function setPot2Miel(mixed $pot2Miel): void
    {
        $this->pot2Miel = $pot2Miel;
    }

    /**
     * @return mixed
     */
    public function getLabelCountry(): ?string
    {
        return $this->labelCountry;
    }

    /**
     * @param mixed $labelCountry
     *
     * @return Contact
     */
    public function setLabelCountry(mixed $labelCountry): self
    {
        $this->labelCountry = $labelCountry;
        return $this;
    }

    /**
     * @return array
     */
    public static function getValidTopics(): array
    {
        return array_keys(Utils::listTopicsContact());
    }
}
