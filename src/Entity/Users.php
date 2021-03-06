<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @UniqueEntity(
 *     fields={"email","username"},
 *      message="Déja utilisé"
 * )
 */
class Users implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8",minMessage="Votre Message doit faire minimum 8 caractères")
     */
    private $password;
    /**
     * @Assert\EqualTo(propertyPath="password",message="Votre mot de passe doit être identique à sa confirmation")
     */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoreFacil;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoreMoyen;

    /**
     * @ORM\Column(type="integer")
     */
    private $scoreDifficil;
    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /** 
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="playerTwo")
     */
    private $games;

    /** 
     * @ORM\Column(type="integer")
     */
    private $Exp;
    
    public function __construct()
    {
        $this->games = new ArrayCollection();
    }
    

    public function getId(): ?int
    {
        return $this->id;
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


    public function getUsername()
    {
        return $this->username;
    }


    public function setUsername($username): void
    {
        $this->username = $username;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    
    public function getScoreFacil(): ?int
    {
        return $this->scoreFacil;
    }

    public function setScoreFacil(int $scoreFacil)
    {
        $this->scoreFacil = $scoreFacil;
    }

    public function getScoreMoyen(): ?int
    {
        return $this->scoreMoyen;
    }

    public function setScoreMoyen(int $scoreMoyen): self
    {
        $this->scoreMoyen = $scoreMoyen;

        return $this;
    }

    public function getScoreDifficil(): ?int
    {
        return $this->scoreDifficil;
    }

    public function setScoreDifficil(int $scoreDifficil)
    {
        $this->scoreDifficil = $scoreDifficil;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setPlayerTwo($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getPlayerTwo() === $this) {
                $game->setPlayerTwo(null);
            }
        }

        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public function setLevel($level): void
    {
        $this->level = $level;
    }

    public function getExp(): ?int
    {
        return $this->Exp;
    }

    public function setExp(int $Exp): self
    {
        $this->Exp = $Exp;

        return $this;
    }
}
