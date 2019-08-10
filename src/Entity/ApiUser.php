<?php


namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ApiUser
 * @package App\Entity
 * @ORM\Entity()
 * @ORM\Table(name="api_users")
 */
class ApiUser implements UserInterface
{

    /**
     * @var
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var
     * @ORM\Column(type="string", unique=true, length=25)
     */
    private $username;

    /**
     * @var
     * @ORM\Column(length=128, type="string")
     */
    private $password;

    /**
     * @var
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @var
     * @ORM\Column(type="string", length=60, unique=true)
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
