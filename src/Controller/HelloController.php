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
     * @Route("/", name="hello")
     */
    public function index(): Response
    {
        return $this->render('hello/index.html.twig', [
            'controller_name' => 'HelloController',
        ]);
    }
    /**
     * @param ForumRepository $repository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/AfficheForum",name="AfficheForum")
     */
    public function showForum(ForumRepository $repository, Request $request)
    {

        $forum = $repository->findAll();
        return $this->render('forum/index.html.twig', ['forum' => $forum]);

    }

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

        return $this->render('forum/show.html.twig', [
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
        return $this->render('forum/addForum.html.twig', array(
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
        return $this->render('forum/updateForum.html.twig', ['form' => $form->createView()]);


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
        return $this->render('commenter/updateComm.html.twig', ['form' => $form->createView()]);


    }


}
