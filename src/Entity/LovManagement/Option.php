<?php

namespace App\Entity\LovManagement;

use App\Model\Lov as baseLov;
use Doctrine\ORM\Mapping as ORM;
use DH\DoctrineAuditBundle\Annotation as Audit;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LovManagement\OptionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})
 */
class Option extends baseLov
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function getId()
    {
        return $this->id;
    }
}
