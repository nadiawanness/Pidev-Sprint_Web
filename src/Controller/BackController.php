<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Offre;
use App\Entity\Recherche;
use App\Entity\Recruteur;
use App\Form\CategorieType;
use App\Form\OffreType;
use App\Form\RecruteurType;
use App\Repository\CategorieRepository;
use App\Repository\OffreRepository;
use App\Repository\RechercheRepository;
use App\Repository\RecruteurRepository;
use Doctrine\DBAL\Types\TextType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class BackController extends AbstractController
{
    /**
     * @Route("/back", name="back")
     */
    public function index(): Response
    {
        return $this->render('back/index.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    /**
     * @Route("/front", name="front", methods={"GET"})
     */
    public function index1(): Response
    {
        return $this->render('front/index.html.twig', [
            'controller_name' => 'BackController',
        ]);
    }
    /**
     * @Route("/categ", name="categ", methods={"GET"})
     */
    public function categ(CategorieRepository $categorieRepository): Response
    {
        return $this->render('back/categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }
    /**
     * @Route("/user", name="user", methods={"GET","POST"})
     */
    public function user(RecruteurRepository $recruteurRepository,Request $request,PaginatorInterface $paginator): Response
    {
        $recruteur = new Recruteur();
        $searchForm = $this->createForm(\App\Form\Search1Type::class,$recruteur);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $nom = $searchForm['nom']->getData();
            $donnees = $recruteurRepository->search1($nom);
            return $this->redirectToRoute('search1', array('nom' => $nom));
        }
        $donnees = $this->getDoctrine()->getRepository(Recruteur::class)->findBy([],['nom' => 'desc']);

        // Paginate the results of the query
        $offres = $paginator->paginate(
        // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        return $this->render('back/user.html.twig', [
            'recruteurs' => $donnees,
            'searchForm' => $searchForm->createView()
        ]);
    }
    /**
     * @Route("/search/{nom}", name="search", methods={"GET","POST"})
     */
    public function search(OffreRepository $offreRepository,$nom,Request $request): Response
    {
        $offre = new Offre();
        $searchForm = $this->createForm(\App\Form\SearchType::class,$offre);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $nom = $searchForm['nom']->getData();
            $donnees = $offreRepository->search($nom);
            return $this->redirectToRoute('search', array('nom' => $nom));
        }
        $offre = $offreRepository->search($nom);
        return $this->render('back/offre_list.html.twig', [
            'offres' => $offre,
            'searchForm' => $searchForm->createView()
        ]);
    }
    /**
     * @Route("/offre_list", name="offre_list", methods={"GET","POST"})
     */
    public function offre(OffreRepository $offreRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $offre = new Offre();
        $searchForm = $this->createForm(\App\Form\SearchType::class,$offre);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $nom = $searchForm['nom']->getData();
            $donnees = $offreRepository->search($nom);
            return $this->redirectToRoute('search', array('nom' => $nom));
        }
        $donnees = $this->getDoctrine()->getRepository(Offre::class)->findBy([],['abn' => 'desc']);

        // Paginate the results of the query
        $offres = $paginator->paginate(
        // Doctrine Query, not results
            $donnees,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        return $this->render('back/offre_list.html.twig', [
            'offres' => $offres,
            'searchForm' => $searchForm->createView()
        ]);
    }
    /**
     * @Route("/listo", name="listo", methods={"GET"})
     */
    public function listo(OffreRepository $offreRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('back/listo.html.twig', [
            'offres' => $offreRepository->findAll(),
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
    /**
     * @Route("/show/{id}", name="show", methods={"GET"})
     */
    public function show_offre(Offre $offre): Response
    {
        return $this->render('back/offre_show.html.twig', [
            'offre' => $offre,
        ]);
    }
    /**
     * @Route("/offre_delete/{id}", name="offre_delete", methods={"DELETE"})
     */
    public function delete1(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offre_list');
    }
    /**
     * @Route("/delete/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(Request $request, Categorie $categorie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categ');
    }
    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['photo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $categorie->setPhoto($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($categorie);
            $entityManager->flush();
            return $this->redirectToRoute('categ');
        }
        return $this->render('back/categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("{id}", name="show1", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        return $this->render('back/categorie/edit.html.twig', [
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
            $uploadedFile = $form['photo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $categorie->setPhoto($filename);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('categ');
        }

        return $this->render('back/categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/user_delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete2(Request $request, Recruteur $recruteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recruteur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recruteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user');
    }
    /**
     * @Route("/listo1", name="listo1", methods={"GET"})
     */
    public function listo1(RecruteurRepository $recruteurRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('back/listo1.html.twig', [
            'recruteurs' => $recruteurRepository->findAll(),
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

    /**
     * @Route("/search1/{nom}", name="search1", methods={"GET","POST"})
     */
    public function search1(RecruteurRepository $recruteurRepository,$nom,Request $request): Response
    {
        $recruteur = new Recruteur();
        $searchForm = $this->createForm(\App\Form\Search1Type::class,$recruteur);
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted()) {
            $nom = $searchForm['nom']->getData();
            $donnees = $recruteurRepository->search1($nom);
            return $this->redirectToRoute('search1', array('nom' => $nom));
        }
        $recruteur = $recruteurRepository->search1($nom);
        return $this->render('back/user.html.twig', [
            'recruteurs' => $recruteur,
            'searchForm' => $searchForm->createView()
        ]);
    }

}
