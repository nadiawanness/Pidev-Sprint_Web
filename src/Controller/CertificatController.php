<?php

namespace App\Controller;

use App\Entity\Certificat;
use App\Form\CertificatType;
use App\Repository\CertificatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/certificat")
 */
class CertificatController extends AbstractController
{
    /**
     * @Route("/", name="certificat_index", methods={"GET"})
     */
    public function index(CertificatRepository $certificatRepository): Response
    {
        return $this->render('certificat/index.html.twig', [
            'certificats' => $certificatRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="certificat_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $certificat = new Certificat();
        $form = $this->createForm(CertificatType::class, $certificat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($certificat);
            $entityManager->flush();

            return $this->redirectToRoute('certificat_index');
        }

        return $this->render('certificat/new.html.twig', [
            'certificat' => $certificat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="certificat_show", methods={"GET"})
     */
    public function show(Certificat $certificat): Response
    {
        return $this->render('certificat/show.html.twig', [
            'certificat' => $certificat,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="certificat_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Certificat $certificat): Response
    {
        $form = $this->createForm(CertificatType::class, $certificat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('certificat_index');
        }

        return $this->render('certificat/edit.html.twig', [
            'certificat' => $certificat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="certificat_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Certificat $certificat): Response
    {
        if ($this->isCsrfTokenValid('delete'.$certificat->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($certificat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('certificat_index');
    }
}
