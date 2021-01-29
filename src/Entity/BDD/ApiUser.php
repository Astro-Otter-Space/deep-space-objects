<?php


namespace App\Entity\BDD;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ApiUser
 * @package App\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="api_users")
 * @ORM\EntityListeners({"App\EventListener\GenerateTokenListener"})
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"email"}, groups={"api_user"}, message="registration.constraint.unique")
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
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\Email(mode="html5", groups={"api_user"}, message="contact.constraint.email")
     * @Assert\NotBlank(groups={"api_user"}, message="contact.constraint.not_blank")
     */
    private $email;

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
     * @ORM\Column(type="json", name="roles")
     */
    private $roles = [];

    /**
     * @var string $pot2Miel
     */
    private $pot2Miel;

    /**
     * @var
     * @Assert\NotBlank(groups={"api_user"}, message="contact.constraint.not_blank")
     * @Assert\NotCompromisedPassword(groups={"api_user"}, message="registration.constraint.compromised_pwd")
     * @Assert\Length(min="6", max="4096", groups={"api_user"}, minMessage="registration.constraint.length")
     */
    private $rawPassword;

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return ['ROLE_API_USER'];
    }


    /**
     * @param mixed $rawPassword
     *
     * @return ApiUser
     */
    public function setRawPassword($rawPassword): self
    {
        $this->rawPassword = $rawPassword;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRawPassword()
    {
        return $this->rawPassword;
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
     * @return mixed
     */
    public function getEmail(): string
    {
        return (string) $this->email;
    }

    /**
     * @param mixed $email
     *
     * @return ApiUser
     */
    public function setEmail($email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPot2Miel():? string
    {
        return $this->pot2Miel;
    }

    /**
     * @param mixed $pot2Miel
     *
     * @return ApiUser
     */
    public function setPot2Miel($pot2Miel): self
    {
        $this->pot2Miel = $pot2Miel;
        return $this;
    }

    /**
     * @param mixed $isActive
     *
     * @return ApiUser
     */
    public function setIsActive($isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

}
