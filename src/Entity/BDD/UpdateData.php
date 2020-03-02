<?php

namespace App\Entity\BDD;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateData
 *
 * @package App\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="update_data")
 * @UniqueEntity(fields={"id"})
 */
class UpdateData
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
     * @ORM\Column(type="datetime", nullable=false)
     * @Assert\DateTime(message="update_data.constraint.datetime")
     */
    private $date;

    /**
     * @var
     * @ORM\Column(type="json", nullable=false)
     */
    private $listDso;

    /**
     * @return mixed
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return UpdateData
     */
    public function setId($id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     *
     * @return UpdateData
     */
    public function setDate($date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getListDso()
    {
        return $this->listDso;
    }

    /**
     * @param mixed $listDso
     *
     * @return UpdateData
     */
    public function setListDso($listDso): self
    {
        $this->listDso = $listDso;
        return $this;
    }


}
