<?php

namespace App\Entity;

use App\Repository\NoticeRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: NoticeRepository::class)]
#[ORM\Table("web_notice")]
class Notice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 300)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $noticeFrom;

    #[ORM\Column(type: 'datetime')]
    private $noticeTo;

    #[ORM\Column(type: "string", columnDefinition: "ENUM('Active', 'Deleted')")]
    private $status;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private $created;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private $updated;

    #[ORM\Column(type: 'string', length: 255)]
    private $file;

    public function __construct()
    {
        $this->status = 'Active';
    }

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

    public function getNoticeFrom(): ?\DateTimeInterface
    {
        return $this->noticeFrom;
    }

    public function setNoticeFrom(\DateTimeInterface $noticeFrom): self
    {
        $this->noticeFrom = $noticeFrom;

        return $this;
    }

    public function getNoticeTo(): ?\DateTimeInterface
    {
        return $this->noticeTo;
    }

    public function setNoticeTo(\DateTimeInterface $noticeTo): self
    {
        $this->noticeTo = $noticeTo;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function isNew(): bool
    {
        $isNew = false;
        $today = new \DateTime();
        $today->modify('-7 day');
        if ($this->getNoticeFrom() > $today) {
            $isNew = true;
        }
        return $isNew;
    }
}
