<?php

namespace App\Entity;

use App\Entity\Post;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Post::class, orphanRemoval: true)]
    private $posts;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $username;

    #[ORM\Column(type: 'string', length: 20)]
    private $phone;

    #[ORM\OneToOne(targetEntity: Address::class, cascade: ['persist', 'remove'])]
    private $address;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Research::class, orphanRemoval: true)]
    private $research;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Favorite::class, orphanRemoval: true)]
    private $favorites;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Message::class, orphanRemoval: true)]
    private $messages;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Chat::class)]
    private $sellerChats;

    #[ORM\OneToMany(mappedBy: 'buyer', targetEntity: Chat::class, orphanRemoval: true)]
    private $buyerChats;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->research = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->sellerChats = new ArrayCollection();
        $this->buyerChats = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
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

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection<int, Research>
     */
    public function getResearch(): Collection
    {
        return $this->research;
    }

    public function addResearch(Research $research): self
    {
        if (!$this->research->contains($research)) {
            $this->research[] = $research;
            $research->setUser($this);
        }

        return $this;
    }

    public function removeResearch(Research $research): self
    {
        if ($this->research->removeElement($research)) {
            // set the owning side to null (unless already changed)
            if ($research->getUser() === $this) {
                $research->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites[] = $favorite;
            $favorite->setUser($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getUser() === $this) {
                $favorite->setUser(null);
            }
        }

        return $this;
    }

    public function isFavorite(Post $post): bool
    {
        foreach ($this->favorites as $favorite) {
            if ($favorite->getPost() === $post) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getSellerChats(): Collection
    {
        return $this->sellerChats;
    }

    public function addSellerChat(Chat $sellerChat): self
    {
        if (!$this->sellerChats->contains($sellerChat)) {
            $this->sellerChats[] = $sellerChat;
            $sellerChat->setSeller($this);
        }

        return $this;
    }

    public function removeSellerChat(Chat $sellerChat): self
    {
        if ($this->sellerChats->removeElement($sellerChat)) {
            // set the owning side to null (unless already changed)
            if ($sellerChat->getSeller() === $this) {
                $sellerChat->setSeller(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Chat>
     */
    public function getBuyerChats(): Collection
    {
        return $this->buyerChats;
    }

    public function addBuyerChat(Chat $buyerChat): self
    {
        if (!$this->buyerChats->contains($buyerChat)) {
            $this->buyerChats[] = $buyerChat;
            $buyerChat->setBuyer($this);
        }

        return $this;
    }

    public function removeBuyerChat(Chat $buyerChat): self
    {
        if ($this->buyerChats->removeElement($buyerChat)) {
            // set the owning side to null (unless already changed)
            if ($buyerChat->getBuyer() === $this) {
                $buyerChat->setBuyer(null);
            }
        }

        return $this;
    }
}
