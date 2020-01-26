<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecondExampleRepository")
 */
class SecondExample
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eg;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEg(): ?string
    {
        return $this->eg;
    }

    public function setEg(string $eg): self
    {
        $this->eg = $eg;

        return $this;
    }
}
