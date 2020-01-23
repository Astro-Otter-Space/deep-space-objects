<?php

namespace App\Entity\BDD;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ItemShared
 *
 * @package App\Entity\BDD
 * @ORM\Entity()
 * @ORM\Table(name="item_shared")
 * @UniqueEntity(fields={"idDso", "astrobinId"}, message="registration.constraint.unique")
 */
class ItemShared
{
    /**
     * @var
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue()
     */
    private $id;

    /**
     * @var
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $idDso;

    /**
     * @var
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $astrobinId;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $createdDate;

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
     * @return ItemShared
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIdDso()
    {
        return $this->idDso;
    }

    /**
     * @param mixed $idDso
     *
     * @return ItemShared
     */
    public function setIdDso($idDso)
    {
        $this->idDso = $idDso;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAstrobinId()
    {
        return $this->astrobinId;
    }

    /**
     * @param mixed $astrobinId
     *
     * @return ItemShared
     */
    public function setAstrobinId($astrobinId)
    {
        $this->astrobinId = $astrobinId;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedDate(): \DateTimeInterface
    {
        return $this->createdDate;
    }

    /**
     * @param \DateTimeInterface $createdDate
     *
     * @return ItemShared
     */
    public function setCreatedDate(\DateTimeInterface $createdDate): ItemShared
    {
        $this->createdDate = $createdDate;
        return $this;
    }

}
