<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TestRepository::class)
 */
class Test
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="json")
     */
    private $question = [];

    /**
     * @ORM\ManyToOne(targetEntity=Skill::class, inversedBy="tests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $skill_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $level;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?array
    {
        return $this->question;
    }

    public function setQuestion(array $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getSkillId(): ?Skill
    {
        return $this->skill_id;
    }

    public function setSkillId(?Skill $skill_id): self
    {
        $this->skill_id = $skill_id;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(?string $level): self
    {
        $this->level = $level;

        return $this;
    }
}
