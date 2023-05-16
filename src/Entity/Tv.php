<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TvRepository::class)]
#[ApiResource]
class Tv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster_path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $backdrop_path = null;

    #[ORM\Column]
    private ?float $vote_average = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $overview = null;

    #[ORM\Column(length: 255)]
    private ?string $first_air_date = null;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'tvs')]
    private Collection $genres;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $original_name = null;

    public function __construct()
    {
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPosterPath(): ?string
    {
        return $this->poster_path;
    }

    public function setPosterPath(?string $poster_path): self
    {
        $this->poster_path = $poster_path;

        return $this;
    }

    public function getBackdropPath(): ?string
    {
        return $this->backdrop_path;
    }

    public function setBackdropPath(?string $backdrop_path): self
    {
        $this->backdrop_path = $backdrop_path;

        return $this;
    }

    public function getVoteAverage(): ?float
    {
        return $this->vote_average;
    }

    public function setVoteAverage(float $vote_average): self
    {
        $this->vote_average = $vote_average;

        return $this;
    }

    public function getOverview(): ?string
    {
        return $this->overview;
    }

    public function setOverview(string $overview): self
    {
        $this->overview = $overview;

        return $this;
    }

    public function getFirstAirDate(): ?string
    {
        return $this->first_air_date;
    }

    public function setFirstAirDate(string $first_air_date): self
    {
        $this->first_air_date = $first_air_date;

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        $this->genres->removeElement($genre);

        return $this;
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

    public function getOriginalName(): ?string
    {
        return $this->original_name;
    }

    public function setOriginalName(string $original_name): self
    {
        $this->original_name = $original_name;

        return $this;
    }
}
