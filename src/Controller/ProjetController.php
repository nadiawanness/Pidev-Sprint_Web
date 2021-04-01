<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\FreelancerRepository;
use App\Repository\ProjetRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client as Client;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/makeoffre", name="makeoffre", methods={"GET"})
     */

    public function makeoffre(Request $request,ProjetRepository $repository,\Swift_Mailer $mailer){
        $projet=$repository->find($request->query->get("id"));
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('ayoumahouesprit@gmail.com')
            ->setTo($projet->getUser()->getUsername())
            ->setBody(
               "Bonjour l'utilisateur ".$this->getUser()->getUsername()." vous contactez a propos votre offre : ".$projet->getNomProjet()
            );
        $mailer->send($message);
        $sid = 'ACa4b6cca03cde40d5cb4ab9e566958e26';
        $token = '65779274df40f74c45a7a2a745c18020';
      //  $client = new Client($sid, $token);

     //   $client->messages->create(
      //      '+21655215711',
  //          [
         //       'from' => '+18452187055',
         //       'body' =>               'Bonjour lutilisateur '.$this->getUser()->getUsername().'vous contactez a propos votre offre : '.$projet->getNomProjet()

        //    ]
      //  );
        $response = new Response("offre bien ajouter");
        return $response;

    }
    /**
     * @Route("/search", name="search", methods={"GET"})
     */

    public function search(Request $request,ProjetRepository $repository,\Swift_Mailer $mailer){

        $result = $repository->createQueryBuilder('o')
            ->where('o.nomProjet  LIKE :product')
            ->setParameter('product', '%'.$request->query->get("id").'%')
            ->getQuery()
            ->getArrayResult();
        return new JsonResponse([
            'projects' => $result
            ]

        );

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
        $projet->setUser($this->getUser());

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

    /**
     * @Route("/back/stat", name="stat")
     */
    public function stat(){
        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();

        $sqlAdmin2 = 'SELECT job_salary , COUNT(*) AS toBeUsed FROM projet  GROUP BY job_salary';
        $stmtAdmin2 = $conn->prepare($sqlAdmin2);


        $stmtAdmin2->execute();
        $arrayAdmin2 = $stmtAdmin2->fetchAll();

        $data2 = array(['salaire','gestion salaires']);
        foreach ($arrayAdmin2 as $item){
            array_push($data2,[$item['job_salary'],intval($item['toBeUsed'])]);
        }

        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable($data2);
        $pieChart->getOptions()->setTitle('Pourcentages des reclamations pour chaque Object');
        $pieChart->getOptions()->setWidth(600);
        $pieChart->getOptions()->setHeight(400);

        return $this->render('projet/satatrec.html.twig', array(

            'arrayAdmin2' => $arrayAdmin2,
            'piechart'=>$pieChart ,

        ));

    }
}
