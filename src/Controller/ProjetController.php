<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\FreelancerRepository;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projet")
 */
class ProjetController extends AbstractController
{
    /**
     * @Route("/index", name="projet_index", methods={"GET"})
     */
    public function index(ProjetRepository $projetRepository): Response
    {
        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }

    /**
     * @Route("/affiche",name="afficheProjets")
     *
     */
    public function freelancersAction(ProjetRepository $repository){
        $projet=$repository->findAll();
        return $this->render("projet/show.html.twig",[
            "projets"=>$projet
        ]);
    }
    /**
     * @Route("/projetliste", name="projetBack", methods={"GET"})
     */

    public function ProjetListe(ProjetRepository $repository){
        $projet=$repository->findAll();
        return $this->render("projet/projetBack.html.twig",[
            "projet"=>$projet
        ]);
    }
    /**
     * @Route("/projetP", name="projetP", methods={"GET"})
     */
    public function Projet(FreelancerRepository $repository){
        $projet=$repository->findAll();
        return $this->render("projet/projetP.html.twig",[
            "projet"=>$projet
        ]);
    }

    /**
     * @Route("/addProjet", name="projet_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('projet/_form.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="projet_show", methods={"GET"})
     */
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="projet_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Projet $projet): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="projet_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Projet $projet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projet);
            $entityManager->flush();
        }


        return $this->redirectToRoute('projet_index');

    }
}
