<?php

namespace App\Entity;

use App\Repository\CVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CVRepository::class)
 */
class CV
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=JobSeeker::class, mappedBy="cv", cascade={"persist", "remove"})
     */
    private $owner;



    /**
     * @ORM\OneToMany(targetEntity=Skill::class, mappedBy="cv")
     */
    private $skills;

    /**
     * @ORM\OneToMany(targetEntity=Formation::class, mappedBy="cv")
     */
    private $fomations;

    /**
     * @ORM\OneToMany(targetEntity=Experience::class, mappedBy="cv")
     */
    private $experiences;

    /**
     * @ORM\OneToMany(targetEntity=Langue::class, mappedBy="cv")
     */
    private $langues;


    public function __construct()
    {
        $this->skills = new ArrayCollection();
        $this->fomations = new ArrayCollection();
        $this->experiences = new ArrayCollection();
        $this->langues = new ArrayCollection();
    }

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
        // unset the owning side of the relation if necessary
        if ($owner === null && $this->owner !== null) {
            $this->owner->setCv(null);
        }

        // set the owning side of the relation if necessary
        if ($owner !== null && $owner->getCv() !== $this) {
            $owner->setCv($this);
        }

        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Skill[]
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills[] = $skill;
            $skill->setCv($this);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): self
    {
        if ($this->skills->removeElement($skill)) {
            // set the owning side to null (unless already changed)
            if ($skill->getCv() === $this) {
                $skill->setCv(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Formation[]
     */
    public function getFomations(): Collection
    {
        return $this->fomations;
    }

    public function addFomation(Formation $fomation): self
    {
        if (!$this->fomations->contains($fomation)) {
            $this->fomations[] = $fomation;
            $fomation->setCv($this);
        }

        return $this;
    }

    public function removeFomation(Formation $fomation): self
    {
        if ($this->fomations->removeElement($fomation)) {
            // set the owning side to null (unless already changed)
            if ($fomation->getCv() === $this) {
                $fomation->setCv(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Experience[]
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setCv($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getCv() === $this) {
                $experience->setCv(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Langue[]
     */
    public function getLangues(): Collection
    {
        return $this->langues;
    }

    public function addLangue(Langue $langue): self
    {
        if (!$this->langues->contains($langue)) {
            $this->langues[] = $langue;
            $langue->setCv($this);
        }

        return $this;
    }

    public function removeLangue(Langue $langue): self
    {
        if ($this->langues->removeElement($langue)) {
            // set the owning side to null (unless already changed)
            if ($langue->getCv() === $this) {
                $langue->setCv(null);
            }
        }

        return $this;
    }
}
