<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="acceuil")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("ajoutevent",name="ajoutevent")
     */



    public function ajoutEvent(Request $request , \Swift_Mailer $mailer)
    {
        $event= new Evenement();
        $form=$this->createForm(EvenementType::class,$event);
        $form= $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){


            $event = $form->getData();
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('noreplay.espritwork@gmail.com')
                ->setTo($event->getEmail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'emails/registration.html.twig',
                        compact('event')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);

            $em=$this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('afficheevent');
        }
        return $this->render('home/ajoutE.html.twig',['form'=>$form->createView()]);
    }
    /**
     * @Route("/afficheevent", name="afficheevent")
     */
    public function afficheEvent(EvenementRepository $repository)
    {
        $event=$repository->findAll();
        $event=$repository->OrderByNom();
        return $this->render('home/afficheE.html.twig', ['event' => $event,]);
    }

    /**
     * @param $id
     * @param EvenementRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/supprimeevent/{id}" , name="supprimeevent")
     */

    function supprimerEvent($id , EvenementRepository $repository){
        $event=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('afficheevent');
    }

    /**
     * @param EvenementRepository $repository
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modifieevent/{id}" , name="modifieevent")
     */

    function modifierEvent(EvenementRepository $repository ,$id , Request $request)

    {

        $event=$repository->find($id);
        $form=$this->createForm(EvenementType::class,$event);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('afficheevent');
        }

        return $this->render('home/modifieE.html.twig' , ['form'=>$form->createView()] );

    }

    /**
     * @param EvenementRepository $repository
     * @param Request $request
     * @return Response
     * @Route("/rechercheevent",name="rechercheevent")
     */

    function RechercheEvent(EvenementRepository $repository , Request $request)
    {
        $nom=$request->get('recherche');
        $event=$repository->RechercheNom($nom);
        return $this->render('home/afficheE.html.twig' , ['event'=>$event]);
    }



}
