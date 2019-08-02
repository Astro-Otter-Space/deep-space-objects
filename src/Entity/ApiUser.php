<?php


namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ApiUser
 * @package App\Entity
 */
class ApiUser implements UserInterface
{

    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $username;

    /**
     * @var
     */
    private $password;

    /**
     * @var
     */
    private $isActive;

    /**
     * @var
     */
    private $email;

    /**
     * ApiUser constructor.
     *
     * @param $username
     */
    public function __construct($username)
    {
        $this->isActive = true;
        $this->username = $username;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return ['ROLE_API_USER'];
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param $password
     *
     * @return ApiUser
     */
    public function setPassword($password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
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

    public function eraseCredentials()
    {
    }

}
