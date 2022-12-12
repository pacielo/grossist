<?php

namespace App\Entity\LovManagement;

use App\Model\Lov as baseLov;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\LovManagement\Ville;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LovManagement\CountryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Country extends baseLov
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Ville::class, mappedBy="country")
     */
    private $villes;


    public function __construct()
    {
        parent::__construct();
        $this->villes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|Ville[]
     */
    public function getVilles()
    {
        return $this->villes;
    }

    public function addVilles(Ville $ville): self
    {
        if (!$this->villes->contains($ville)) {
            $this->villes[] = $ville;
            $ville->setCountry($this);
        }

        return $this;
    }

    public function removeVilles(Ville $ville): self
    {
        if ($this->villes->removeElement($ville)) {
            if ($ville->getCountry() === $this) {
                $ville->setCountry(null);
            }
        }

        return $this;
    }
}
