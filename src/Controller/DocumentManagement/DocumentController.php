<?php

namespace App\Controller\DocumentManagement;

use App\Entity\DocumentManagement\Document;
use App\Form\DocumentManagement\DocumentType;
use App\Form\DocumentManagement\DocumentEditType;
use App\Repository\DocumentManagement\DocumentRepository;

use Doctrine\DBAL\DBALException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 */
class DocumentController extends AbstractController
{
	private $kernel;
    private $messageBus;
    private $slugger;
    private $translator;

    public function __construct(MessageBusInterface $messageBus, TranslatorInterface $translator, KernelInterface $kernel, SluggerInterface $slugger, ParameterBagInterface $params)
    {
        $this->params = $params;
        $this->kernel = $kernel;
        $this->messageBus = $messageBus;  
        $this->slugger = $slugger;  
        $this->translator = $translator;
    }
	
    /**
     * @Route("/admin/document/index", name="admin_document_index", methods={"GET"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function index(DocumentRepository $documentRepository): Response
    {
        return $this->render('DocumentManagement/Document/index.html.twig', [
            'documents' => $documentRepository->findBy(['isDeleted' => false], ['id'=>'ASC']),
        ]);
    }
	
    /**
     * @Route("/admin/document/new", name="admin_document_new", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function new(Request $request, TranslatorInterface $translator, KernelInterface $kernel): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            try { 
                $document->setCreateUser($this->getUser());
                $document->setUpdateUser($this->getUser());  
                             
                $entityManager->persist($document);
                $entityManager->flush();

                $this->addFlash('success', $translator->trans('document.flash.created'));
				
                return $this->redirectToRoute('admin_document_index', ['document'=>$document->getId()]);
            } catch (DBALException $exception) {
                $this->addFlash('error', $exception->getMessage());
            } catch (Throwable $exception) {
                $this->addFlash('error', $exception->getMessage());
            }             
        }

        return $this->render('DocumentManagement/Document/new.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/document/edit/{id}", name="admin_document_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function edit(Request $request, Document $document, TranslatorInterface $translator, KernelInterface $kernel): Response
    {

        $form = $this->createForm(DocumentEditType::class, $document);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $document->setUpdateUser($this->getUser());
			$uploadFile = $form->get('file')->getData();
			if ($uploadFile) {
                $originalFilename = pathinfo($uploadFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $this->slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadFile->guessExtension();

                try {
					//var_dump($newFilename);exit();
                    $uploadFile->move($this->kernel->getProjectDir() .'/data/', $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', $e->getMessage());
                }
                $document->setFileUri($newFilename);
            }

            try {
                $em->persist($document);
                $em->flush();

                $this->addFlash('success', $translator->trans('document.flash.updated'));

                return $this->redirectToRoute('admin_document_index');
            } catch (DBALException $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('DocumentManagement/Document/edit.html.twig', [
            'document' => $document,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Desactive/Active un document
     * @Route("/admin/document/disable/{id}", name="admin_document_disable", methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function disable(Document $document, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();

        if ($document->getIsValid()) {
            $document->setIsValid(false);
            $this->addFlash('success', $translator->trans('document.flash.disable'));
        } else {
            $document->setIsValid(true);
            $this->addFlash('success', $translator->trans('document.flash.enable'));
        }

        try {
            $em->persist($document);
            $em->flush();
        } catch (DBALException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('admin_document_index');
    }
	
	 /**
     * return a security private upload.
     *
     * @Route("/admin/document/upload/{format}/{upload}", defaults={"format": "origin"}, name="admin_private_upload", methods="GET")	 
     * @Security("is_granted('ROLE_GERANT') or is_granted('ROLE_TRANS') or is_granted('ROLE_ADMIN')")
     */
    public function upload(Request $request, string $upload, string $format="origin", KernelInterface $kernel): Response 
    {
        $fs = new Filesystem();

        if ($format == 'origin') {
            $filePath = $kernel->getProjectDir() . '/data/' . $upload;            
        } else {
            $filePath = $kernel->getProjectDir() . '/data/'. $format .'/' . $upload;
        }
        
        if ($fs->exists($filePath)) {       
            $response = new BinaryFileResponse($filePath);
                $response->headers->set('Content-Type', mime_content_type($filePath));
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_INLINE,
                    basename($filePath)
                );
                return $response;            
        } 

        $filePath = $kernel->getProjectDir() . '/data/not-found.png';
        if ($fs->exists($filePath)) {       
            $response = new BinaryFileResponse($filePath);
                $response->headers->set('Content-Type', mime_content_type($filePath));
                $response->setContentDisposition(
                    ResponseHeaderBag::DISPOSITION_INLINE,
                    basename($filePath)
                );
                return $response;            
        } 
        throw new FileNotFoundException($filePath);
    }
	
	function clean_text($str, $options = array()){

		if(in_array('TOUT',$options)):
			$options = array('HTML','TRIM','MAJUSCULE','MINUSCULE','ACCENT','PONCTUATION','TABULATION','ENTER','DOUBLE');
		endif;
		foreach($options as $option):
			switch($option){
			/*
				// Suppression des espaces vides en debut et fin de chaque ligne
				case 'TRIM':
					$str = preg_replace("#^[\t\f\v ]+|[\t\f\v ]+$#m",'',$str);
				break;

				// Remplacement des caractères accentués par leurs équivalents non accentués
				case 'ACCENT':
					$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
					$str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
					$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. 'œ'
					$str = html_entity_decode($str); 
				break;

				// Transforme tout le texte en minuscule
				case 'MINUSCULE':
					$str = mb_strtolower($str, 'UTF-8');
				break;

				// Transforme tout le texte en majuscule
				case 'MAJUSCULE':
					$str = mb_strtoupper($str, 'UTF-8');
				break;

				// Remplace toute la ponctuation par des espaces
				case 'PONCTUATION':
					$str = preg_replace('#([[:punct:]])#',' ',$str);
					$exceptions = array("’");
					$str = str_replace($exceptions,' ',$str);
				break;
				*/
				// Remplace les tabulations par des espaces
				case 'TABULATION':
					$str = preg_replace("#\h#u", " ", $str);
				break;

				// Remplace les espaces multiples par des espaces simples
				case 'DOUBLE':
					$str = preg_replace('#[" "]{2,}#',' ',$str);
				break;

				// Remplace 1 entrée (\r\n) par 1 espace
				case 'ENTER':
					$str = str_replace(array("\r","\n"),' ',$str);
				break;
				/*
				// Supprime toutes les balises html
				case 'HTML':
					$str = strip_tags($str);
				break;
				*/
			}
		endforeach;
		return $str;
	}
}
