<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Form\EvenementType;
use App\Repository\CategorieRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

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

            $value=0;
            $event->setJaime($value);
            $event->setJaimepas($value);
            $event->setNbp($value);

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
    public function afficheEvent(EvenementRepository $repository , Request $request , PaginatorInterface $paginator )
    {
        $event=$repository->findAll();
        $event=$repository->OrderByNom();


        $event = $paginator->paginate(

            $event,//on passe les donnees
            $request->query->getInt('page',1),
            4

        );




        return $this->render('home/afficheE.html.twig',compact('event'));
    }


    /**
     * @Route("/filterevent", name="filterevent")
     */
    public function filterEvent(EvenementRepository $repository , Request $request , PaginatorInterface $paginator )
    {
        $event=$repository->findAll();
        $event=$repository->Categorie();


        $event = $paginator->paginate(

            $event,//on passe les donnees
            $request->query->getInt('page',1),
            4

        );
        return $this->render('home/afficheE.html.twig',compact('event'));
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
     * @Route("/participeevent/{id}", name="participeevent")
     */
    public function participeEvent(EvenementRepository $repository , $id , Request $request)
    {
        $event=$repository->find($id);
        $new_nb=$event->getNbp() + 1;
        $event->setNbp($new_nb);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        $request
            ->getSession()
            ->getFlashBag()
            ->add('participe', ' Votre participation est enregistre avec succes');


        return $this->redirectToRoute('afficheevent');
    }


    /**
     * @param $id
     * @param EvenementRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeevent/{id}", name="likeevent")
     */
    public function likeEvent(EvenementRepository $repository , $id )
    {
        $event=$repository->find($id);
        $new=$event->getJaime() + 1;
        $event->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('afficheevent');
    }

    /**
     * @param $id
     * @param EvenementRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeevent/{id}", name="dislikeevent")
     */
    public function dislikeEvent(EvenementRepository $repository , $id )
    {
        $event=$repository->find($id);
        $new=$event->getJaimepas() + 1;
        $event->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('afficheevent');
    }







}
