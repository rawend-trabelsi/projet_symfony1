<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class Profile
{
    private string $username;

    #[Assert\Email]
    private string $email;
    private string $city;

    #[Assert\Regex(pattern: '/^[0-9]{5}$/', message: 'Postcode must be in format XXXXX')]
    private int $postcode;
    private string $street;
    private int $number;
    private string $phone;
    private ?string $currentPassword;

    private ?string $newPassword;

    #[Assert\EqualTo(propertyPath: 'newPassword')]
    private string $newPasswordConfirmation;

    public function __construct(User $user)
    {
        $this->username = $user->getUsername();
        $this->email = $user->getEmail();
        $this->phone = $user->getPhone();
        $address = $user->getAddress();
        $this->city = $address->getCity();
        $this->postcode = $address->getPostcode();
        $this->street = $address->getStreet();
        $this->number = $address->getNumber();
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

    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $currentPassword): self
    {
        $this->currentPassword = $currentPassword;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode): self
    {
        $this->postcode = $postcode;
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

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;
        return $this;
    }

    public function getNewPasswordConfirmation(): ?string
    {
        return $this->newPasswordConfirmation;
    }

    public function setNewPasswordConfirmation(string $newPasswordConfirmation): self
    {
        $this->newPasswordConfirmation = $newPasswordConfirmation;
        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;
        return $this;
    }
}
