<?php

namespace OHMedia\AlertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OHMedia\AlertBundle\Repository\AlertRepository;
use OHMedia\UtilityBundle\Entity\BlameableEntityTrait;

#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert
{
    use BlameableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function __toString(): string
    {
        return 'Alert #'.$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
