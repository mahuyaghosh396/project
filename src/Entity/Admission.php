<?php

namespace App\Entity;

use App\Repository\AdmissionRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

#[ORM\Entity(repositoryClass: AdmissionRepository::class)]

class Admission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\Column(type: 'string', length: 100)]
    private $email;

    #[ORM\Column(type: 'string', length: 50)]
    private $phone_no;

    #[ORM\Column(type: 'string', length: 150)]
    private $Adress;

    #[ORM\Column(type: 'integer', length: 150)]
    private $zip_no;

    #[ORM\Column(type: 'string', length: 150)]
    private $district;

    #[ORM\Column(type: 'string', length: 255)]
    private $state;

    #[ORM\Column(type: 'string', length: 255)]
    private $trade;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNo(): ?string
    {
        return $this->phone_no;
    }

    public function setPhoneNo(string $phone_no): self
    {
        $this->phone_no = $phone_no;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->Adress;
    }

    public function setAdress(string $Adress): self
    {
        $this->Adress = $Adress;

        return $this;
    }

    public function getZipNo(): ?int
    {
        return $this->zip_no;
    }

    public function setZipNo(int $zip_no): self
    {
        $this->zip_no = $zip_no;

        return $this;
    }

    public function getDistrict(): ?string
    {
        return $this->district;
    }

    public function setDistrict(string $district): self
    {
        $this->district = $district;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getTrade(): ?string
    {
        return $this->trade;
    }

    public function setTrade(string $trade): self
    {
        $this->trade = $trade;

        return $this;
    }
}
