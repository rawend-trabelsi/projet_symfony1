<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Post::class, orphanRemoval: true)]
    private $posts;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Research::class, orphanRemoval: true)]
    private $research;

    public function __construct()
    {
        $this->research = new ArrayCollection();
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
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

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
            $research->setCategory($this);
        }

        return $this;
    }

    public function removeResearch(Research $research): self
    {
        if ($this->research->removeElement($research)) {
            // set the owning side to null (unless already changed)
            if ($research->getCategory() === $this) {
                $research->setCategory(null);
            }
        }

        return $this;
    }
}
