<?php

namespace App\Entity;

use App\Repository\ScoreRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ScoreRepository::class)
 */
class Score
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=JobSeeker::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Test::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $test_id;

    /**
     * @ORM\Column(type="float")
     */
    private $score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?JobSeeker
    {
        return $this->owner;
    }

    public function setOwner(?JobSeeker $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTestId(): ?Test
    {
        return $this->test_id;
    }

    public function setTestId(?Test $test_id): self
    {
        $this->test_id = $test_id;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }
}
