<?php

namespace App\Entity\UserManagement;

use App\Entity\LovManagement\Civility;
use App\Entity\LovManagement\Commune;
use App\Entity\LovManagement\Gender;
use App\Entity\LovManagement\GenreCommerce;
use App\Entity\LovManagement\Quartier;
use App\Entity\LovManagement\TypeCommerce;
use App\Entity\LovManagement\Ville;
use App\Entity\TransactionManagement\Livraison;
use App\Entity\TransactionManagement\Product;
use App\Entity\TransportManagement\Vehicule;
use App\Traits\ActorTrait;
use App\Traits\DateTrait;
use App\Traits\IsValidTrait;
use App\Traits\IsEnableTrait;
use App\Validator\UserManagement\ComplexPassword; 
use App\Validator\UserManagement\VerificationRpps; 
use DH\DoctrineAuditBundle\Annotation as Audit;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\File;
use App\Entity\TransactionManagement\Purchase;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserManagement\UserRepository")
 * @UniqueEntity("email")
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})
 */
class User implements UserInterface, \JsonSerializable
{
    use ActorTrait, DateTrait, IsValidTrait, IsEnableTrait;

    protected $listOfRoles;

    /**
     * Requests older than this many seconds will be considered expired, une demande toutes les 4h => 1 minute
     */
    public const RETRY_TTL = 60;
    /**
     * Maximum time that the confirmation token will be valid. lien actif 30 min  
     */
    public const TOKEN_TTL = 1800;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(min=2, max=50)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=100, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank(groups={"Registration", "User"})
     * @ORM\OrderBy({"firstname" = "DESC"})
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=100, nullable=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 100,
     *      minMessage = "Your last name must be at least {{ limit }} characters long",
     *      maxMessage = "Your last name cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank(groups={"Registration", "User"})
     * @ORM\OrderBy({"lastname" = "DESC"})
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="tel", type="string", length=40, nullable=true)
     * @Assert\Length(
     *      min = 6,
     *      max = 40,
     *      minMessage = "Your phone must be at least {{ limit }} characters long",
     *      maxMessage = "Your phone cannot be longer than {{ limit }} characters"
     * )
     * @Assert\NotBlank(groups={"Registration", "User"})
     */
    private $tel;

    /**
     * @var string
     *
     * @ORM\Column(name="societe", type="string", length=40, nullable=true)
     */
    private $societe;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LovManagement\Civility")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank(groups={"Registration", "User"})
     */
    protected $civility;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\LovManagement\Gender")
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    protected $gender;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(min="2")
     * @ComplexPassword(groups={"Registration", "PasswordReset"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank(groups={"Registration", "User"})
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\ManyToOne(targetEntity="Group")
     * @ORM\JoinColumn(nullable=true)     
     */
    protected $group;

    /**
     * @ORM\Column(name="has_accepted_CGU", type="boolean")
     * @Assert\NotBlank(groups={"Registration"})
     */
    protected $hasAcceptedCGU = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $confirmation_token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $password_requested_at;

    /**
     * @var bool
     *
     * @ORM\Column(name="change_password", type="boolean", nullable=true)
     */
    protected $change_password = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_password", type="datetime", nullable=true)
     */
    protected $lastChangePassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginError;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbLoginError;

    /**
     * @var Purchase[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\TransactionManagement\Purchase", mappedBy="buyer", cascade={"remove"})
     */
    private $purchases;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * It only stores the name of the file which stores the contract subscribed
     * by the user.
     *
     * @ORM\Column(type="string", length=255, nullable=true))
     *
     * @var string
     */
    private $contract;

    /**
     * @ORM\ManyToMany(targetEntity=Vehicule::class, mappedBy="proprietaire")
     */
    private $vehicules;

    /**
     * @ORM\OneToMany(targetEntity=Livraison::class, mappedBy="transporteur")
     */
    private $livraisons;

    /**
     * @ORM\ManyToMany(targetEntity=TypeCommerce::class, inversedBy="users")
     */
    private $typeCommerce;

    /**
     * @ORM\ManyToMany(targetEntity=GenreCommerce::class)
     */
    private $genreCommerce;

    /**
     * @ORM\ManyToMany(targetEntity=Quartier::class)
     */
    private $quartier;

    /**
     * @ORM\ManyToMany(targetEntity=Commune::class)
     */
    private $commune;

    /**
     * @ORM\ManyToMany(targetEntity=Ville::class)
     */
    private $ville;

    /**
     * @ORM\ManyToMany(targetEntity=Product::class, mappedBy="distributeur")
     */
    private $products;

    public function __construct()
    {
        $this->isValid = true;
        $this->isEnable = false;
        $this->lastChangePassword = null;
        $this->listOfRoles = array(); 
        $this->active = false;
        $this->purchases = new ArrayCollection();
        $this->isActive = true;
        $this->vehicules = new ArrayCollection();
        $this->livraisons = new ArrayCollection();
        $this->typeCommerce = new ArrayCollection();
        $this->genreCommerce = new ArrayCollection();
        $this->quartier = new ArrayCollection();
        $this->commune = new ArrayCollection();
        $this->ville = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    public function __toString()
    {
        if ($this->lastname != '' && $this->firstname != '') {
            return $this->lastname . ' ' . $this->firstname;
        }

        return "Utilisateur";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setFirstName(string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function getLastName(): ?string
    {
        return $this->lastname;
    }

    public function setLastName(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): void
    {
        $this->tel = $tel;
    }

    public function getSociete(): ?string
    {
        return $this->societe;
    }

    public function setSociete(string $societe): void
    {
        $this->societe = $societe;
    }

    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

    public function setCivility(Civility $civility): void
    {
        $this->civility = $civility;
    }

    public function setGender(Gender $gender): void
    {
        $this->gender = $gender;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
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

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRoles(array $roles): self
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role): self
    {
        $role = mb_strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }
        
    public function removeRole(string $role): self
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return \in_array(mb_strtoupper($role), $this->getRoles(), true);
    }

    public function setGroup(Group $group): void
    {
        $this->group = $group;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setHasAcceptedCGU(bool $hasAcceptedCGU)
    {
        $this->hasAcceptedCGU = $hasAcceptedCGU;

        return $this;
    }

    public function getHasAcceptedCGU(): ?bool
    {
        return $this->hasAcceptedCGU;
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function setListOfRoles($listOfRoles)
    {
        $this->listOfRoles = $listOfRoles;
    }

    public function getListOfRoles()
    {
        $this->listOfRoles = $this->roles; 
        return $this->listOfRoles;
    }



    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmation_token;
    }

    public function setConfirmationToken(?string $confirmation_token): self
    {
        $this->confirmation_token = $confirmation_token;

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->password_requested_at;
    }

    public function setPasswordRequestedAt(?\DateTimeInterface $password_requested_at): self
    {
        $this->password_requested_at = $password_requested_at;

        return $this;
    }

    public function isPasswordRequestNonExpired(int $ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
            $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * Set change_password
     *
     * @param bool $change_password
     * @return self
     */
    public function setChangePassword(bool $change_password): ?self
    {
        $this->change_password = $change_password;

        return $this;
    }

    /**
     * Get change_password
     *
     * @return bool
     */
    public function getChangePassword(): ?bool
    {
        return $this->change_password;
    }

    /**
     * Set lastChangePassword
     *
     * @param \DateTime $lastChangePassword
     * @return User
     */
    public function setLastChangePassword($lastChangePassword)
    {
        $this->lastChangePassword = $lastChangePassword;

        return $this;
    }

    /**
     * Get lastChangePassword
     *
     * @return \DateTime
     */
    public function getLastChangePassword()
    {
        return $this->lastChangePassword;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'firstname' => $this->getFirstName(),
            'lastname' => $this->getLastName(),
            'email' => $this->getEmail(),
            'tel' => $this->getTel(),
        ];
    }

    public function getLastLoginError(): ?\DateTimeInterface
    {
        return $this->lastLoginError;
    }

    public function setLastLoginError(?\DateTimeInterface $lastLoginError): self
    {
        $this->lastLoginError = $lastLoginError;

        return $this;
    }

    public function getNbLoginError(): ?int
    {
        if($this->nbLoginError == null){
            return 0;
        }
        return $this->nbLoginError;
    }

    public function setNbLoginError(?int $nbLoginError): self
    {
        $this->nbLoginError = $nbLoginError;

        return $this;
    }

        /**
     * Set active.
     *
     * @param bool $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param Purchase[] $purchases
     */
    public function setPurchases($purchases)
    {
        $this->purchases = $purchases;
    }

    /**
     * @return Purchase[]
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @param Livraison[] $livraisons
     */
    public function setLivraisons($livraison)
    {
        $this->livraisons = $livraison;
    }

    /**
     * @return Livraison[]
     */
    public function getLivraisons()
    {
        return $this->livraisons;
    }


    /**
     * @param File $contract
     */
    public function setContractFile(File $contract = null)
    {
        $this->contractFile = $contract;
    }

    /**
     * @return File
     */
    public function getContractFile()
    {
        return $this->contractFile;
    }

    /**
     * @param string $contract
     */
    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    /**
     * @return string
     */
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @return Collection|Vehicule[]
     */
    public function getVehicules(): Collection
    {
        return $this->vehicules;
    }

    public function addVehicule(Vehicule $vehicule): self
    {
        if (!$this->vehicules->contains($vehicule)) {
            $this->vehicules[] = $vehicule;
            $vehicule->addProprietaire($this);
        }

        return $this;
    }

    public function removeVehicule(Vehicule $vehicule): self
    {
        if ($this->vehicules->removeElement($vehicule)) {
            $vehicule->removeProprietaire($this);
        }

        return $this;
    }

    /**
     * @return Collection|TypeCommerce[]
     */
    public function getTypeCommerce(): Collection
    {
        return $this->typeCommerce;
    }

    public function addTypeCommerce(TypeCommerce $typeCommerce): self
    {
        if (!$this->typeCommerce->contains($typeCommerce)) {
            $this->typeCommerce[] = $typeCommerce;
        }

        return $this;
    }

    public function removeTypeCommerce(TypeCommerce $typeCommerce): self
    {
        $this->typeCommerce->removeElement($typeCommerce);

        return $this;
    }

    /**
     * @return Collection|GenreCommerce[]
     */
    public function getGenreCommerce(): Collection
    {
        return $this->genreCommerce;
    }

    public function addGenreCommerce(GenreCommerce $genreCommerce): self
    {
        if (!$this->genreCommerce->contains($genreCommerce)) {
            $this->genreCommerce[] = $genreCommerce;
        }

        return $this;
    }

    public function removeGenreCommerce(GenreCommerce $genreCommerce): self
    {
        $this->genreCommerce->removeElement($genreCommerce);

        return $this;
    }

    /**
     * @return Collection|Quartier[]
     */
    public function getQuartier(): Collection
    {
        return $this->quartier;
    }

    public function addQuartier(Quartier $quartier): self
    {
        if (!$this->quartier->contains($quartier)) {
            $this->quartier[] = $quartier;
        }

        return $this;
    }

    public function removeQuartier(Quartier $quartier): self
    {
        $this->quartier->removeElement($quartier);

        return $this;
    }

    /**
     * @return Collection|Commune[]
     */
    public function getCommune(): Collection
    {
        return $this->commune;
    }

    public function addCommune(Commune $commune): self
    {
        if (!$this->commune->contains($commune)) {
            $this->commune[] = $commune;
        }

        return $this;
    }

    public function removeCommune(Commune $commune): self
    {
        $this->commune->removeElement($commune);

        return $this;
    }

    /**
     * @return Collection|Ville[]
     */
    public function getVille(): Collection
    {
        return $this->ville;
    }

    public function addVille(Ville $ville): self
    {
        if (!$this->ville->contains($ville)) {
            $this->ville[] = $ville;
        }

        return $this;
    }

    public function removeVille(Ville $ville): self
    {
        $this->ville->removeElement($ville);

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addDistributeur($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            $product->removeDistributeur($this);
        }

        return $this;
    }

 
}
