<?php

namespace App\Entity;

use App\Repository\FeedBackRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FeedBackRepository::class)
 */
class FeedBack
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $rate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=JobSeeker::class, inversedBy="feedbacks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $jobSeeker;

    /**
     * @ORM\ManyToOne(targetEntity=Employeur::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $writer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getJobSeeker(): ?JobSeeker
    {
        return $this->jobSeeker;
    }

    public function setJobSeeker(?JobSeeker $jobSeeker): self
    {
        $this->jobSeeker = $jobSeeker;

        return $this;
    }

    public function getWriter(): ?Employeur
    {
        return $this->writer;
    }

    public function setWriter(?Employeur $writer): self
    {
        $this->writer = $writer;

        return $this;
    }
}
