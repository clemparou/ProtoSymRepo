<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Projet;
use App\Entity\Comment;
use App\Repository\ProjetRepository;
use App\Form\ProjetType;
use App\Form\CommentType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Common\Persistence\ObjectManager;



class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ProjetRepository $repo)
    {
    	$projets = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'projets' => $projets
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home(TranslatorInterface $translator){
        $blogTitle = $translator->trans('blog.title', [], null, 'en');
    	return $this->render('blog/home.html.twig', [
            'blogtitle' => $blogTitle
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Projet $projet = null, Request $request, ObjectManager $manager) {

    	if(!$projet) {
    		$projet = new Projet();
    	}

    	$form = $this->createForm(ProjetType::class, $projet);

		$form -> handleRequest($request);

		if($form->isSubmitted() && $form->isValid()) {
			if(!$projet->getId()){
				$projet->setCreatedAt(new \DateTime());
			}

			$manager->persist($projet);
			$manager->flush();

			return $this->redirectToRoute('blog_show', ['id' => $projet->getId() ] );
		}


    	return $this->render('blog/create.html.twig', [
    		'formProjet' => $form->createView(),
    		'editMode' => $projet->getId() !== null
    	]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Projet $Projet, ProjetRepository $repo, $id, Request $request, ObjectManager $manager){

    	$projet = $repo->find($id); 
    	
    	$comment = new Comment();

    	$form = $this->createForm(CommentType::class, $comment);

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()){
    		$comment->setCreatedAt( new \DateTime())
    				->setProjet($projet);
    		$manager->persist($comment);
    		$manager->flush();

    		return $this->redirectToRoute('blog_show', ['id' => $projet->getId()]);
    	}
    	
    	

    	return $this->render('blog/show.html.twig', [
    		'projet' => $projet,
    		'commentForm' => $form->createView()
    	]);
    }

    
}
