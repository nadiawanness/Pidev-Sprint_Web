<?php
namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Comment;
use App\Entity\Offre;
use App\Entity\Postuler;
use App\Entity\Recruteur;
use App\Form\CandidatType;
use App\Form\CategorieType;
use App\Form\CommentType;
use App\Form\CreateType;
use App\Form\OffreType;
use App\Form\RecruteurType;
use App\Form\ResetPassType;
use App\Repository\CategorieRepository;
use App\Repository\CommentRepository;
use App\Repository\OffreRepository;
use App\Repository\PostulerRepository;
use App\Repository\RecruteurRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class FrontController extends AbstractController
{
    /**
     * @Route("/oubli-pass", name="app-forgotten-password")
     */
    public function forgottenPass(Request $request, RecruteurRepository $recruteurRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createForm(ResetPassType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()){
            //recupere les donnes
            $donnees = $form->getData() ;
            //on cherche si lutilisateur a cet email
            $user = $recruteurRepository->findOneByMail($donnees['mail']) ;

            if(!$user){
                $this->addFlash('danger','cette adresse nexiste pas') ;
                $this->redirectToRoute('Login') ;
            }

            //on genere un token
            $token = $tokenGenerator->generateToken() ;
            try{
                $user->setResetToken($token) ;
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreure est survenue : ' . $exception->getMessage());
                $this->redirectToRoute('Login') ;
            }

            //on genere lurl du mot de passe
            $url= $this->generateURL('app_reset_password',['token'=>$token],
                UrlGeneratorInterface::ABSOLUTE_URL) ;

            //on envoie le message
            $message = (new \Swift_Message('Mot de passe oublie'))
                //ili bech yeb3ath
                ->setFrom('mohamedwael.belhadj@esprit.tn')
                //ili bech ijih l message
                ->setTo($user->getMail())
                ->setBody(
                    "<p>bonjour, </p><p> une demande de reinitialisation de mot de passe a ete effectue . veuillez cliquer sur le lien suivant : ". $url . '</p>',
                    'text/html'
                )
            ;
            //on envoi l email
            $mailer->send($message) ;
            $this->addFlash('success','un email de reinitialisation de mot de passe vous a ete envoye') ;
            return $this->redirectToRoute('Login') ;
        }
        return $this->render('front/forgotten_password.html.twig', ['emailForm'=>$form->createView()]) ;
    }

    /**
     * @Route("/reset_pass/{token}", name="app_reset_password")
     */
    public function resetPassword($token, Request $request){
        //on chercher lutilisateur avec le token
        $user = $this->getDoctrine()->getRepository(Recruteur::class)->findOneBy(['reset_token'=>$token]) ;
        if(!$user){
            $this->addFlash('danger','token inconnu') ;
            return $this->redirectToRoute('Login') ;
        }
        //si le formulaire est envoyee en methode post
        if($request->isMethod('POST')) {
            //on suprime le token
            $user->setResetToken(null);
            //on chiffre le mot de passe
            $user->setPassword($user,$request->request->get('password'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success','mot de passe modifiee avec succes') ;

            return $this->redirectToRoute('Login') ;
        }else{
            return $this->render('offre/reset_password.html.twig',['token'=>$token]) ;
        }


    }
    /**
     * @Route("/front", name="front")
     */
    public function index(CategorieRepository $categorieRepository,Request $request,RecruteurRepository $repository): Response
    {
        $recruteur = new Recruteur();
        $form=$this->createForm(RecruteurType::class,$recruteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recruteurCheck = $repository->findOneBy(['mail' => $recruteur->getMail()]);
            if($recruteur->getMdp()==$recruteurCheck->getMdp())
            {
                $session= new Session();
                $session->set('id',$recruteurCheck->getId());
                $session->set('nom',$recruteurCheck->getNom());
                $session->set('type',$recruteurCheck->getType());
                $session->set('mail',$recruteur->getMail());
                $session->set('competence',$recruteurCheck->getCompetence());
            }

        }
        return $this->render('front/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'form' => $form->createView(),

        ]);
    }
    /**
     * @Route("/Logout", name="Logout")
     */
    public function Logout(Request $request)
    {
        $session = $request->getSession();
        $session->clear();
        return $this->redirectToRoute('front');
    }
    /**
     * @Route("/offredelete1/", name="post", methods={"GET","POST"})
     */
    public function post(Request $request, Offre $offre,RecruteurRepository $repository): Response
    {
        $offre = new Offre();
        $value=$repository->find($this->get('session')->get('id'));

        return $this->redirectToRoute('offre1');
    }
    /**
     * @Route("/addrec", name="addrec", methods={"GET","POST"})
     */
    public function addrec(Request $request,RecruteurRepository $repository): Response
    {
        $recruteur = new Recruteur();
        $form = $this->createForm(CreateType::class, $recruteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['photo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $recruteur->setPhoto($filename);
            $recruteur->setType('recruteur');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recruteur);
            $entityManager->flush();

            return $this->redirectToRoute('Login');
        }
        return $this->render('/front/recruteur_type.html.twig', [
            'recruteurs' => $repository->findAll(),'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/addcand", name="addcand", methods={"GET","POST"})
     */
    public function addcand(Request $request,RecruteurRepository $repository): Response
    {
        $recruteur = new Recruteur();
        $form = $this->createForm(CandidatType::class, $recruteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['photo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $recruteur->setPhoto($filename);
            $recruteur->setType('candidat');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recruteur);
            $entityManager->flush();

            return $this->redirectToRoute('Login');
        }
        return $this->render('/front/candidat_type.html.twig', [
            'recruteurs' => $repository->findAll(),'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/addjob", name="addjob", methods={"GET","POST"})
     */
    public function addjob(CategorieRepository $categorieRepository,Request $request,RecruteurRepository $repository): Response
    {
        $offre = new Offre();
        $value=$repository->find($this->get('session')->get('id'));
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['logo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $offre->setLogo($filename);
            $offre->addRecruteur($value);
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
     * @Route("/profil_det", name="profil_det", methods={"GET"})
     */
    public function profil_det(RecruteurRepository $recruteurRepository): Response
    {
        return $this->render('/front/profil_det.html.twig', [
            'recruteurs' => $recruteurRepository->findAll(),
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
     * @Route("/login", name="Login")
     */
    public function login(Request $request,RecruteurRepository $repository)
    {
        $recruteur = new Recruteur();
        $form=$this->createForm(RecruteurType::class,$recruteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recruteurCheck = $repository->findOneBy(['mail' => $recruteur->getMail()]);
            if($recruteur->getMdp()==$recruteurCheck->getMdp())
            {
                $session= new Session();
                $session->set('id',$recruteurCheck->getId());
                $session->set('nom',$recruteurCheck->getNom());
                $session->set('mail',$recruteur->getMail());
                $session->set('type',$recruteurCheck->getType());
            }

        }
        return $this->render('/front/login.html.twig', [
            'form' => $form->createView(),
        ]);

    }
    /**
     * @Route("/make/{id}", name="make", methods={"GET","POST"})
     */
    public function make(OffreRepository $offreRepository,$id,Request $request,RecruteurRepository $repository,CommentRepository $commentRepository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $offre = $entityManager->getRepository(Offre::class)->find($id);
        $value = $offre->getAbn();
        $value = $value + 1 ;
        $offre->setAbn($value);
        $entityManager->flush();
        $comment = new Comment();
        $session = $request->getSession();
        $form1 = $this->createForm(CommentType::class,$comment);
        $form1->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setOffre($offre)
                ->setAuthorName($this->get('session')->get('mail'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('make',['id'=>$id]);
        }
        $recruteur = new Recruteur();
        $form=$this->createForm(RecruteurType::class,$recruteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recruteurCheck = $repository->findOneBy(['mail' => $recruteur->getMail()]);
            if($recruteur->getMdp()==$recruteurCheck->getMdp())
            {
                $session= new Session();
                $session->set('id',$recruteurCheck->getId());
                $session->set('nom',$recruteurCheck->getNom());
                $session->set('type',$recruteurCheck->getType());
                $session->set('mail',$recruteur->getMail());
            }

        }
        $offretype = $offreRepository->findBy(['id' => $id]);
        $offrepost = $commentRepository->findBy(['offre'=>$offre]);
        return $this->render('/front/make.html.twig', [
            'comments'=> $offrepost,
            'offres' => $offretype,
            'form' => $form->createView(),
            'commentForm'=>$form1->createView(),
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

    /**
     * @Route("edit1/{id}", name="edit1", methods={"GET","POST"})
     */
    public function edit1(Request $request, Offre $offre,OffreRepository $offreRepository,$id): Response
    {
        /*$value =   $offre->getNb();
        $value ++ ;
        $offre->setNb($value);*/
        $offre=$offreRepository->find($id);
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['logo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $offre->setLogo($filename);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('offre1');
        }
        return $this->render('front/offre/edit1.html.twig', [
            'offre' => $offre,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/offredelete1/{id}", name="offredelete1", methods={"DELETE"})
     */
    public function delete(Request $request, Offre $offre): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offre->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('offre1');
    }

    /**
     * @Route("/offre/{id}/like", name="post_like")
     * @param Offre $offre
     * @param ObjectManager $manager
     * @param PostulerRepository $postulerRepository
     * @param RecruteurRepository $repository
     * @return Response
     */
    public function like(Offre $offre,PostulerRepository $postulerRepository,RecruteurRepository $repository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user=$repository->find($this->get('session')->get('id'));
        if(!$user) return $this->json([
            'code'=>403,
            'message'=>"Unauthorized"
        ],403);
        if($offre->isLikedByRecruteur($user))
        {
            $like = $postulerRepository->findOneBy([
                'offre'=>$offre,
                'recruteur'=>$user
            ]);

            $entityManager->remove($like);
            $entityManager->flush();
            return $this->json([
                'code'=>200,
                'message'=>'like bien supprimé',
                'likes'=>$postulerRepository->count(['offre'=>$offre])
            ],200);
        }
        $like = new Postuler();
        $like->setOffre($offre);
        $like->setRecruteur($user);
        $entityManager->persist($like);
        $entityManager->flush();
    return  $this->json([
        'code'=>200,
        'message'=>'ca marche',
        'likes'=>$postulerRepository->count(['offre'=>$offre])
        ],200);
    }





}

