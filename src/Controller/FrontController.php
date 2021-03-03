<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Offre;
use App\Form\CategorieType;
use App\Form\OffreType;
use App\Repository\CategorieRepository;
use App\Repository\OffreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
class FrontController extends AbstractController
{
    /**
     * @Route("/front", name="front")
     */
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('front/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/addjob", name="addjob", methods={"GET","POST"})
     */
    public function addjob(CategorieRepository $categorieRepository,Request $request): Response
    {
        $offre = new Offre();
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['logo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $offre->setLogo($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('offre1');
        }
        return $this->render('/front/addjob.html.twig', [
            'categories' => $categorieRepository->findAll(),'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/offre1", name="offre1", methods={"GET"})
     */
    public function offre1(OffreRepository $offreRepository): Response
    {
        return $this->render('/front/offre.html.twig', [
            'offres' => $offreRepository->findAll(),
        ]);
    }
    /**
     * @Route("/type/{type}", name="type", methods={"GET"})
     */
    public function Type(OffreRepository $offreRepository,$type): Response
    {
        $offretype = $offreRepository->findBy(['idcategoriy' => $type]);
        return $this->render('/front/type.html.twig', [
            'offres' => $offretype,
        ]);
    }





    /**
     * @Route("{id}", name="show1", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        return $this->render('back/categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Categorie $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('categ');
        }

        return $this->render('back/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }
}

