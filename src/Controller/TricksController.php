<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TricksController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */

    public function getTrickList(): Response
    {
        $trickList = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findall();

        return $this->render('tricks/home.html.twig', [
            'title' => 'Home',
            'trickList' => $trickList
        ]);
    }

    /**
     * @Route("/tricks/{id}", name="trick_details", methods={"GET"})
     */
    public function getTrick(Trick $trick) : Response
    {
        return $this->render('tricks/trick_details.html.twig', [
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/create_trick", name="create_trick", methods={"GET", "POST"})
     * @Route("/{id}/edit_trick", name="edit_trick", methods={"GET", "POST"})
     */
    public function manageTrickForm(Trick $newTrick = null, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        if (!$newTrick)
        {
            $newTrick = new Trick();
        }  
        
        $form = $this->createForm(TrickType::class, $newTrick, ['csrf_protection' => false]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$newTrick->getId())
            {
                $newTrick->setCreatedAt(new \DateTime()); 
            }

            $newTrick->setModifiedAt(new \DateTime());   

            $entityManager->persist($newTrick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_details', ['id' => $newTrick->getId()]);
        }

        return $this->render('tricks/trick_form.html.twig', [
            'formTrick' => $form->createView(),
            'editMode' => $newTrick->getId() !== null
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete_trick", methods={"GET","POST"})
     */
    public function deleteTrick(Request $request, Trick $trick): Response
    {
        /*if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }*/

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($trick);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
