<?php

namespace App\Controller;

use App\Entity\Commenter;
use App\Entity\Forum;
use App\Form\CommenterType;
use App\Form\ForumType;
use App\Repository\CommenterRepository;
use App\Repository\ForumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController
{
    /**
     * @Route("/front", name="hello")
     */
    public function index(): Response
    {
        return $this->render('front/hello/index.html.twig', [
            'controller_name' => 'HelloController',
        ]);
    }
   // /**
     //* @param ForumRepository $repository
     //* @param Request $request
     //* @return \Symfony\Component\HttpFoundation\Response
     //* @Route("/AfficheForum",name="AfficheForum")
     //*/
    //public function showForum(ForumRepository $repository, Request $request)
    //{


      //  $forum = $repository->findBy([],['date' => 'ASC']);
        //return $this->render('front/forum/index.html.twig', ['forum' => $forum]);

    //}

    /**
     *
     *  @param Request $request
     *  @param Forum $forum
     * @Route("/forum/{id}/show", name="forum_show")
     */
    public function show( Request $request, Forum $forum)
    {
        $comm = new Commenter();
        $comm->setForum($forum);

        $form = $this -> createForm(CommenterType::class, $comm);
        $form -> handleRequest($request);
        if ($form -> isSubmitted() and $form -> isValid()) {
            $em = $this -> getDoctrine() -> getManager();
            $em -> persist($comm);
            $em -> flush();
            return $this -> redirectToRoute('forum_show', ['id' => $forum->getId() ]);

        }


        return $this->render('front/forum/show.html.twig', [
            'forum' => $forum,
            'form' => $form->createView()
        ]);


    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("addForum",name="addForum")
     */
    public function addForum(Request $request)
    {
        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($forum);
            $em->flush();
            return $this->redirectToRoute('AfficheForum');

        }
        return $this->render('front/forum/addForum.html.twig', array(
            'form' => $form->createView()
        ));


    }
    /**
     * @param $id
     * @param ForumRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/supp/{id}" , name="d")
     */
    function deleteForum($id, ForumRepository $repository)
    {
        $forum = $repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($forum);
        $em->flush();
        return $this->redirectToRoute('AfficheForum');


    }

    /**
     * @param ForumRepository $repository
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/UpdateForum/{id}", name="u")
     */

    function updateForum(ForumRepository $repository, $id, Request $request)
    {
        $forum = $repository->find($id);
        $form = $this->createForm(ForumType::class, $forum);
        $form->add('modifier',SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('AfficheForum');

        }
        return $this->render('front/forum/updateForum.html.twig', ['form' => $form->createView()]);


    }



    /**
     * @param $ref
     * @param CommenterRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route ("/suppCommentaire/{ref}" , name="dcf")
     */
     function deleteComm($ref, CommenterRepository $repository)
    {
        $comm = $repository->find($ref);
        $id=$comm->getForum()->getId();

            $em = $this->getDoctrine()->getManager();
            $em->remove($comm);
            $em->flush();

    return $this->redirectToRoute('forum_show',array('id'=>$id));
    }

    /**
     * @param CommenterRepository $repository
     * @param $ref
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("/UpdateComm/{ref}", name="uc")
     */

    function updateComm (CommenterRepository $repository, $ref, Request $request)
    {
      $comm = $repository->find($ref);
        $id=$comm->getForum()->getId();
    $form = $this->createForm(CommenterType::class, $comm);
    $form->add('modifier',SubmitType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() and $form->isValid()) {
    $em = $this->getDoctrine()->getManager();
    $em->flush();
    return $this->redirectToRoute('forum_show',array('id'=>$id));

    }
        return $this->render('front/commenter/updateComm.html.twig', ['form' => $form->createView()]);


    }


    /**
     * @param ForumRepository $repository
     * @return Response
     * @Route ("forum/tri")
     */

    function OrderBysujetsQL (ForumRepository $repository) {
        $forum=$repository->OrderBysujetQB ();
        return $this->render('front/forum/index.html.twig', ['forum'=>$forum]);
    }


    /**
     * @param ForumRepository $repository
     * @param Request $request
     * @return Response
     * @Route ("forum/searchs", name="rechercheForum")
     */

    function SearchS (ForumRepository $repository,Request $request) {
        $sujet=$request->get('search');
        $forum=$repository->searchS($sujet);
        return $this->render('front/forum/index.html.twig', [
            'pagination' => false,
            'forum'=>$forum
            ]);



       /* $limit=1;
        $page=(int)$request->query->get("page",1);
        $forums=$repository->paginatedAnnonces($page,$limit, $sujet);
        $total=$repository->getTotalAnnonces();


        return $this->render('front/forum/index.html.twig',[
            'pagination' => false,
            'forum'=> $forums,

        ]);*/








    }

   // /**
     //* @param ForumRepository $annRepo
     //* @return Response
     //* @Route ("/stats", name="stats")
     //*/

       // public function statistiques(ForumRepository $annRepo){
         //   $forum = $annRepo->countByDate();
           // $dates = [];
            //$annoncesCount = [];
            //foreach($forum as $foru){

              //  $dates [] = $foru['date'];
                //$annoncesCount[] = $foru['count'];
            //}
            //return $this->render('front/forum/stats.html.twig', [
              //  'dates' => $dates,
                //'annoncesCount' => $annoncesCount
            //]);
    /**
     * @param CommenterRepository $annoncesRepo
     * @param ForumRepository $catRepo
     * @param Request $request
     * @return Response
     * @Route ("/page", name="pagina")
     */

    public function pagination(CommenterRepository $annoncesRepo, ForumRepository $catRepo, Request $request){
        // On définit le nombre d'éléments par page
        $limit = 2;

        // On récupère le numéro de page
        $page = (int)$request->query->get("page", 1);

        // On récupère les filtres
        $filters = $request->get("forum");

        // On récupère les annonces de la page en fonction du filtre
        $annonces = $annoncesRepo->paginatedAnnonces($page, $limit, $filters);

        // On récupère le nombre total d'annonces
        $total = $annoncesRepo->getTotalAnnonces($filters);

        // On vérifie si on a une requête Ajax ya khra hedhy ghalta
       // if($request->get('ajax')){
         //   return new JsonResponse([
           //     'content' => $this->renderView('annonces/_content.html.twig', compact('annonces', 'total', 'limit', 'page'))
            //]);
        //}

        // On va chercher toutes les catégories
        $categories = $catRepo->findAll();

        return $this->render('front/forum/pagination.html.twig',
            [
                'forum' => $categories,
                'commentaire' =>  $annonces,
                'total'=> $total,
                'limit'=> $limit ,
                 'page'=> $page
            ]);
    }

    /**
     * @param ForumRepository $repository
     * @param Request $request
     * @param Forum $forum
     * @Route("/AfficheForum",name="AfficheForum")
     */


    public function paginationP(ForumRepository $repository,Request $request)
    {
        $limit=1;
        $page=(int)$request->query->get("page",1);
        $comm=$repository->paginatedAnnonces($page,$limit);
        $total=$repository->getTotalAnnonces();


        return $this->render('front/forum/index.html.twig',[
            'pagination' => true,
            'forum'=> $comm,
            'total'=>$total,
            'limit'=>$limit,
            'page'=>$page

        ]);


    }



}
