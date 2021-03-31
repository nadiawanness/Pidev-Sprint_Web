<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    /**
     * @Route("/afficheevent", name="afficheevent")
     */
    public function afficheEvent(EvenementRepository $repository)
    {
        $event=$repository->findAll();
        $event=$repository->OrderByNom();
        return $this->render('evenement/afficheE.html.twig', ['event' => $event,]);
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
     * @Route("/ajoutcat",name="ajoutcat")
     */

    public function ajoutCat(Request $request)
    {
        $categorie= new Categorie();
        $form=$this->createForm(CategorieType::class,$categorie);
        $form= $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute('affichecat');
        }
        return $this->render('categorie/ajoutC.html.twig',['form'=>$form->createView()]);
    }
    /**
     * @Route("/affichecat", name="affichecat")
     */
    public function afficheCat(CategorieRepository $repository)
    {
        $categorie=$repository->findAll();
        $categorie=$repository->OrderByNom();
         return $this->render('categorie/afficheC.html.twig', ['categorie' => $categorie,]);
    }

    /**
     * @param $id
     * @param CategorieRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/supprimecat/{id}" , name="supprimecat")
     */

    function supprimerCat($id , CategorieRepository $repository){
        $categorie=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($categorie);
        $em->flush();
        return $this->redirectToRoute('affichecat');
    }

    /**
     * @param CategorieRepository $repository
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modifiecat/{id}" , name="modifiecat")
     */

    function modifierCat(CategorieRepository $repository ,$id , Request $request)

    {

        $categorie=$repository->find($id);
        $form=$this->createForm(CategorieType::class,$categorie);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('affichecat');
        }

        return $this->render('categorie/modifieC.html.twig' , ['form'=>$form->createView()] );

    }

    /**
     * @param CategorieRepository $repository
     * @param Request $request
     * @return Response
     * @Route("/recherchecat",name="recherchecat")
     */

    function RechercheCat(CategorieRepository $repository , Request $request)
    {
        $nom=$request->get('recherche');
        $categorie=$repository->RechercheNom($nom);
        return $this->render('categorie/afficheC.html.twig' , ['categorie'=>$categorie]);
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
        return $this->render('evenement/afficheE.html.twig' , ['event'=>$event]);
    }

}
