<?php

namespace App\Controller;

use App\Entity\Certificat;
use App\Entity\Test;
use App\Form\TestType;
use App\Repository\CertificatRepository;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/test")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/", name="test_index", methods={"GET"})
     */
    public function index(TestRepository $testRepository): Response
    {
        return $this->render('test/index.html.twig', [
            'tests' => $testRepository->findAll(),
        ]);
    }
    /**
     * @return Response
     * @Route("/listT/", name="test_1", methods={"GET"})
     */
    public function listT(TestRepository $testRepository): Response
    {
        return $this->render('test/testT.html.twig', [
            'tests' => $testRepository->findAll(),
        ]);
    }

    /**
     * @Route("/test/new", name="test_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('test_index');
        }

        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="test_show", methods={"GET"})
     */
    public function show(Test $test): Response
    {
        return $this->render('test/show.html.twig', [
            'test' => $test,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="test_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Test $test): Response
    {
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('test_index');
        }

        return $this->render('test/edit.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/del/{id}", name="test_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Test $test): Response
    {
        if ($this->isCsrfTokenValid('delete'.$test->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($test);
            $entityManager->flush();
        }

        return $this->redirectToRoute('test_index');
    }
    /**
     * @Route("/trouver/{id}", name="trouver")
     */
    public function Valider(Request $request,$id,TestRepository $repositorys)
    {
       /* $repository = $this->getDoctrine()->getrepository(Test::Class);//recuperer repisotory
        $offre=$repositorys->find($id);*/
        $tests = $repositorys->findBy(
            ['id'=> $id]
        );
        $test= $repositorys->find($id);
        return $this->render('test/newFront.html.twig', [
            'tests' => $tests,
        ]);//liasion twig avec le controller
    }
    /**
     * @Route("/trouver2/{id}", name="trouver2")
     */
    public function Correct(Request $request,$id,TestRepository $repositorys)
    {

        $repository = $this->getDoctrine()->getrepository(Certificat::Class);//recuperer repisotory


        $certificat = $repository->findBy(
            ['test' => $id]
        );
        return $this->render('test/congrats.html.twig', [
            'certificats' => $certificat,
        ]);//liasion twig avec le controller

    }
    /**
     * @Route("/listo/backe", name="listo", methods={"GET"})
     */
    public function listo(TestRepository $testRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('test/index.html.twig', [
            'tests' => $testRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }


}
