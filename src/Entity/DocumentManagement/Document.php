<?php

namespace App\Entity\DocumentManagement;

use App\Traits\ActorTrait;
use App\Traits\DateTrait;
use App\Traits\IsValidTrait;
use App\Traits\IsDeletedTrait;
use App\Repository\DocumentManagement\DocumentRepository;
use DH\DoctrineAuditBundle\Annotation as Audit;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @Audit\Auditable
 * @Audit\Security(view={"ROLE_ADMIN"})    
 * @Vich\Uploadable 
 */
class Document
{
    use ActorTrait;
    use DateTrait;
    use IsValidTrait;  
    use IsDeletedTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;
	
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $version;
	
	/**
     * @Vich\UploadableField(mapping="uploads_private", fileNameProperty="fileUri")
     * @var File
     */
    protected $file;
	
	 /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fileUri;
	
	/**
     * @ORM\Column(type="string", length=255)
     */
    private $folder;
	
    public function __construct()
    {
       
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }
	
    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }
	public function getFileUri(): ?string
    {
        return $this->fileUri;
    }

    public function setFileUri(?string $fileUri): self
    {
        $this->fileUri = $fileUri;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(File $file = null): self
    {
        $this->file = $file;
        return $this;
    }


    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }
}
