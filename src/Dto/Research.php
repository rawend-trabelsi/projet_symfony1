<?php

namespace App\Dto;

use App\Entity\Category;

class Research
{
    private Category $category;
    private ?string $query = '';
    private ?int $page = 1;
    private ?int $maxCost = 9999999;
    private ?int $minCost = 0;
    private ?string $postcode = '';

    public function __construct()
    {
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;
        return $this;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function getMaxCost(): ?int
    {
        return $this->maxCost;
    }

    public function setMaxCost(?int $maxCost): self
    {
        $this->maxCost = $maxCost;
        return $this;
    }

    public function getMinCost(): ?int
    {
        return $this->minCost;
    }

    public function setMinCost(?int $minCost): self
    {
        $this->minCost = $minCost;
        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;
        return $this;
    }
}
