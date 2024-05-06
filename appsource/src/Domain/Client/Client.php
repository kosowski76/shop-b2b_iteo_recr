<?php
namespace App\Domain\Client;

use App\Domain\Purchase\Purchase;
use App\Domain\Purchase\PurchaseItem;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    protected Uuid $id;
    protected string $username;
    protected string $email;
    protected string $password;
    protected Collection $BankAccounts;
    protected Collection $purchases;
    protected $roles = [];
/*
 ** This part 'isActive' to refactor **/
    protected bool $isActive;
    protected DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->isActive = true;
        $this->purchases = new ArrayCollection();
        $this->roles = $this->getRole();
    }
    public function getId(): Uuid
    {
        return $this->id;
    }
    public function setId(Uuid $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
        // return (string)$this->email;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }
    public function getBankAccounts(): Collection
    {
        return $this->BankAccounts;
    }
    public function setBankAccounts(Collection $BankAccounts): void
    {
        $this->BankAccounts = $BankAccounts;
    }
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }
    public function setPurchases(ArrayCollection $purchases): self
    {
        $this->purchases = $purchases;

        return $this;
    }
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
    public function getRole(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function getRoles(): array
    {
        return $this->roles;
    }
    public function getSalt()
    {
        return null;
    }
    public function eraseCredentials(): void
    {
    }
    /**
     * @param Purchase $purchase
     * @throws Exception
     */
    public function makePurchases(Purchase $purchase)
    {
        $purchase = new Purchase();
        $purchase->setClient($this);

        $purchase->setCreatedAt(new DateTime());

        $this->purchases->add($purchase);
        $purchase->addPurchaseItem(new PurchaseItem());
    }
}
