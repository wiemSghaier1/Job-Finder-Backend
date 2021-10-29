<?php

namespace App\Entity;

use App\Repository\JobSeekerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=JobSeekerRepository::class)
 * @ORM\HasLifecycleCallbacks() 
 */
class JobSeeker implements UserInterface
{
    use Timestamps;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Exclude()

     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="json",nullable=true)
     */
    private $roles = [];
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $fieldsOfInterests = [];

    /**
     * @ORM\OneToOne(targetEntity=CV::class, inversedBy="owner", cascade={"persist", "remove"})
     */
    private $cv;


    /**
     * @ORM\OneToMany(targetEntity=FeedBack::class, mappedBy="jobSeeker", orphanRemoval=true)
     */
    private $feedbacks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stripeId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subsciptionId;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $subscription_end_at;


    public function __construct()
    {
        $this->feedbacks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getFieldsOfInterests(): ?array
    {
        return $this->fieldsOfInterests;
    }

    public function setFieldsOfInterests(?array $fieldsOfInterests): self
    {
        $this->fieldsOfInterests = $fieldsOfInterests;

        return $this;
    }

    public function getCv(): ?CV
    {
        return $this->cv;
    }

    public function setCv(?CV $cv): self
    {
        $this->cv = $cv;

        return $this;
    }



    /**
     * @return Collection|FeedBack[]
     */
    public function getFeedbacks(): Collection
    {
        return $this->feedbacks;
    }

    public function addFeedback(FeedBack $feedback): self
    {
        if (!$this->feedbacks->contains($feedback)) {
            $this->feedbacks[] = $feedback;
            $feedback->setJobSeeker($this);
        }

        return $this;
    }

    public function removeFeedback(FeedBack $feedback): self
    {
        if ($this->feedbacks->removeElement($feedback)) {
            // set the owning side to null (unless already changed)
            if ($feedback->getJobSeeker() === $this) {
                $feedback->setJobSeeker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Test[]
     */
    public function getScore(): Collection
    {
        return $this->score;
    }

    public function addScore(Test $score): self
    {
        if (!$this->score->contains($score)) {
            $this->score[] = $score;
        }

        return $this;
    }

    public function removeScore(Test $score): self
    {
        $this->score->removeElement($score);

        return $this;
    }
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getStripeId(): ?string
    {
        return $this->stripeId;
    }

    public function setStripeId(?string $stripeId): self
    {
        $this->stripeId = $stripeId;

        return $this;
    }

    public function getSubsciptionId(): ?string
    {
        return $this->subsciptionId;
    }

    public function setSubsciptionId(?string $subsciptionId): self
    {
        $this->subsciptionId = $subsciptionId;

        return $this;
    }

    public function getSubscriptionEndAt(): ?\DateTimeInterface
    {
        return $this->subscription_end_at;
    }

    public function setSubscriptionEndAt(?\DateTimeInterface $subscription_end_at): self
    {
        $this->subscription_end_at = $subscription_end_at;

        return $this;
    }
}
