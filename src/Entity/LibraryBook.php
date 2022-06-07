<?php

namespace App\Entity;

use App\Repository\LibraryBookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LibraryBookRepository::class)]
class LibraryBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $author;

    #[ORM\Column(type: 'string', length: 255)]
    private $available_book;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAvailableBook(): ?string
    {
        return $this->available_book;
    }

    public function setAvailableBook(string $available_book): self
    {
        $this->available_book = $available_book;

        return $this;
    }
}
