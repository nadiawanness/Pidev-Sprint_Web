<?php

namespace App\Controller;

use App\Entity\Freelancer;
use App\Form\FreelancerType;

use App\Repository\FreelancerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/back", name="back")
     */
    public function index2(): Response
    {
        return $this->render('home/indexBack.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/freelancers",name="freelancers")
     *
     */
    public function freelancersAction(FreelancerRepository $repository){
        $freelancers=$repository->findAll();
        return $this->render("home/freelancers.html.twig",[
            "freelancers"=>$freelancers
        ]);
    }

    /**
     * @Route("/addFreelancers", name="addFreelancer")
     *
     *
     */
    public function addFreelancer(Request $request){
        $freelancer = new Freelancer();
        $form=$this->createForm(FreelancerType::class , $freelancer);
        $form->add("password",PasswordType::class);
        $form->add("prix");
        $form->add("photo");
        $form->add("photocover");
        $form->add("skills");
        $form->add("country");
        $form->add("education");
        $form->add("experience");

        $form = $form->handleRequest($request);
        if($form->isSubmitted() and $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->persist($freelancer);
            $em->flush();
            return $this->redirectToRoute('home');
        }
        return $this->render("freelancer/addFreelancer.html.twig" , array(
            'form'=>$form->createView()

        ));
    }
    /**
     * @Route("/edit{id}", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, FreelancerRepository $repository,$id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Freelancer::class);

        $freelancer = $repository->find($id);
        $form = $this->createForm(FreelancerType::class, $freelancer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('freelancer/edit.html.twig', [
            'freelancer' => $freelancer,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/liste", name="freelancerBack", methods={"GET"})
     */

    public function freelancersListe(FreelancerRepository $repository){
        $freelancers=$repository->findAll();
        return $this->render("home/freelancersBack.html.twig",[
            "freelancers"=>$freelancers
        ]);
    }
    /**
     * @Route("/hhh{id}", name="freelancer_del", methods={"DELETE"})
     */
    public function delete(Request $request, Freelancer $freelancer): Response
    {
        if ($this->isCsrfTokenValid('delete'.$freelancer->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($freelancer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('freelancerBack');
    }
}
