<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GenreRepository::class)]
#[ApiResource]
class Genre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Movie::class, inversedBy: 'genres')]
    private Collection $movie;

    #[ORM\ManyToMany(targetEntity: Tv::class, mappedBy: 'genres')]
    private Collection $tvs;

    public function __construct()
    {
        $this->movie = new ArrayCollection();
        $this->tvs = new ArrayCollection();
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

    /**
     * @return Collection<int, Movie>
     */
    public function getMovie(): Collection
    {
        return $this->movie;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movie->contains($movie)) {
            $this->movie->add($movie);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        $this->movie->removeElement($movie);

        return $this;
    }

    /**
     * @return Collection<int, Tv>
     */
    public function getTvs(): Collection
    {
        return $this->tvs;
    }

    public function addTv(Tv $tv): self
    {
        if (!$this->tvs->contains($tv)) {
            $this->tvs->add($tv);
            $tv->addGenre($this);
        }

        return $this;
    }

    public function removeTv(Tv $tv): self
    {
        if ($this->tvs->removeElement($tv)) {
            $tv->removeGenre($this);
        }

        return $this;
    }
}
