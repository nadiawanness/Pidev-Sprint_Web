<?php

namespace App\Controller;

use App\Entity\Commenter;
use App\Form\CommenterType;
use App\Repository\CommenterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/commenter")
 */
class CommenterController extends AbstractController
{
    /**
     * @Route("/", name="commenter_index", methods={"GET"})
     */
    public function index(CommenterRepository $commenterRepository): Response
    {
        return $this->render('commenter/index.html.twig', [
            'commenters' => $commenterRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="commenter_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $commenter = new Commenter();
        $form = $this->createForm(CommenterType::class, $commenter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commenter);
            $entityManager->flush();

            return $this->redirectToRoute('commenter_index');
        }

        return $this->render('commenter/new.html.twig', [
            'commenter' => $commenter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commenter_show", methods={"GET"})
     */
    public function show(Commenter $commenter): Response
    {
        return $this->render('commenter/show.html.twig', [
            'commenter' => $commenter,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commenter_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Commenter $commenter): Response
    {
        $form = $this->createForm(CommenterType::class, $commenter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('commenter_index');
        }

        return $this->render('commenter/edit.html.twig', [
            'commenter' => $commenter,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commenter_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Commenter $commenter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commenter->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($commenter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commenter_index');
    }
}
