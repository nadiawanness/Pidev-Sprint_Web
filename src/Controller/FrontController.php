<?php
namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Categorie;
use App\Entity\Certificat;
use App\Entity\Comment;
use App\Entity\Commenter;
use App\Entity\Evenement;
use App\Entity\Forum;
use App\Entity\Offre;
use App\Entity\Postuler;
use App\Entity\Projet;
use App\Entity\Reclamation;
use App\Entity\Recruteur;
use App\Form\CalendarType;
use App\Form\CandidatType;
use App\Form\CategorieType;
use App\Form\CommenterType;
use App\Form\CommentType;
use App\Form\CreateType;
use App\Form\EvenementType;
use App\Form\ForumType;
use App\Form\FreelancerType;
use App\Form\OffreType;
use App\Form\ProjetType;
use App\Form\ReclamationType;
use App\Form\RecruteurType;
use App\Repository\CalendarRepository;
use App\Repository\CategorieRepository;
use App\Repository\CertificatRepository;
use App\Repository\CommenterRepository;
use App\Repository\CommentRepository;
use App\Repository\EvenementRepository;
use App\Repository\ForumRepository;
use App\Repository\OffreRepository;
use App\Repository\PostulerRepository;
use App\Repository\ProjetRepository;
use App\Repository\ReclamationRepository;
use App\Repository\RecruteurRepository;
use App\Repository\TestRepository;
use Doctrine\Persistence\ObjectManager;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swift_SmtpTransport;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\HttpFoundation\Session\Session;
use Twilio\Rest\Client as Client;

class FrontController extends AbstractController
{
    /**
     * @Route("/front/", name="front")
     */
    public function index(CategorieRepository $categorieRepository,Request $request,RecruteurRepository $repository,OffreRepository $offreRepository): Response
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
            'offres'=>$offreRepository->findBy([],['abn' => 'asc']),
            'categories' => $categorieRepository->findAll(),
            'form' => $form->createView(),

        ]);
    }
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route ("addForum",name="addForum")
     */
    public function addForum(Request $request, RecruteurRepository $repository)
    {
        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $value=$repository->find($this->get('session')->get('id'));
            $forum->setRecruteur($value);
            $em = $this->getDoctrine()->getManager();
            $em->persist($forum);
            $em->flush();
            return $this->redirectToRoute('AfficheForum');

        }
        return $this->render('/front/forum/addForum.html.twig', array(
            'form' => $form->createView()
        ));


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


        return $this->render('/front/forum/index.html.twig',[
            'pagination' => true,
            'forum'=> $comm,
            'total'=>$total,
            'limit'=>$limit,
            'page'=>$page

        ]);


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
        return $this->render('/front/forum/index.html.twig', [
            'pagination' => false,
            'forum'=>$forum
        ]);
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
     *
     *  @param Request $request
     *  @param Forum $forum
     * @Route("/forum/{id}/show", name="forum_show")
     */
    public function show8( Request $request, Forum $forum,RecruteurRepository $repository)
    {
        $comm = new Commenter();
        $comm->setForum($forum);
        $form = $this -> createForm(CommenterType::class, $comm);
        $form -> handleRequest($request);
        if ($form -> isSubmitted() and $form -> isValid()) {
            $value=$repository->find($this->get('session')->get('id'));
            $comm->setRecruteur($value);
            $em = $this -> getDoctrine() -> getManager();
            $em -> persist($comm);
            $em -> flush();
            return $this -> redirectToRoute('forum_show', ['id' => $forum->getId() ]);

        }


        return $this->render('/front/forum/show.html.twig', [
            'forum' => $forum,
            'form' => $form->createView()
        ]);


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
     * @Route ("UpdateComm/UpdateComm/{ref}", name="UpdateComm")
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
     * @param EvenementRepository $repository
     * @param Request $request
     * @return Response
     * @Route("/rechercheevent",name="rechercheevent")
     */

    function RechercheEvent(EvenementRepository $repository , Request $request)
    {
        $nom=$request->get('recherche');
        $event=$repository->RechercheNom($nom);
        return $this->render('/front/Event/recherche.html.twig' , ['event'=>$event]);
    }
    /**
     * @Route("accepte/{id}",name="accepte", methods={"GET","POST"})
     */
    public function accepte(PostulerRepository $postulerRepository,$id,RecruteurRepository $recruteurRepository):Response
    {
        $test = "accepter";
        $entityManager = $this->getDoctrine()->getManager();
        $vale = $recruteurRepository->find($id);
        $val = $entityManager->getRepository(Postuler::class)->findOneBy(['recruteur'=>$vale]);
        $postuler = $entityManager->getRepository(Postuler::class)->find($val);
        $val->setAccepte($test);
        $entityManager->flush();
        return $this->render('/front/offre/content.html.twig');
    }
    /**
     * @Route("confirmer/{id}",name="confirmer", methods={"GET","POST"})
     */
    public function confirmer(\Swift_Mailer $mailer,PostulerRepository $postulerRepository,$id,RecruteurRepository $recruteurRepository): Response
    {

        $val = $recruteurRepository->find($id);
        $postuler = $postulerRepository->findBy(['recruteur'=>$val]);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('/front/offre/listP.html.twig', [
            'postulers' => $postuler,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        // Store PDF Binary Data
        $output = $dompdf->output();

        // In this case, we want to write the file in the public directory
        $publicDirectory = $this->getParameter('upload_directory');
        // e.g /var/www/project/public/mypdf.pdf
        $pdfFilepath =  $publicDirectory . '/mypdf.pdf';

        // Write file to the desired path
        file_put_contents($pdfFilepath, $output);

        // Send some text response

        $message = (new \Swift_Message('Confirmation for offer'))
            ->setFrom('noreplay.espritwork@gmail.com')
            ->setTo($val->getMail())
            ->setBody(
                $this->renderView(
                // templates/front/offre/confirm.html.twig
                    '/front/offre/confirm.html.twig'
                    , [
                    'postulers' => $postuler,
                ]),
                'text/html'
            )
            ->attach(\Swift_Attachment::fromPath($pdfFilepath))
        ;
        $mailer->send($message);
        return $this->redirectToRoute('update');
    }
    /**
     * @Route("ajoutevent",name="ajoutevent")
     */
    public function ajoutEvent(Request $request , \Swift_Mailer $mailer,RecruteurRepository $repository)
    {
        $event= new Evenement();
        $form=$this->createForm(EvenementType::class,$event);
        $form= $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            $value=0;
            $event->setJaime($value);
            $event->setJaimepas($value);
            $event->setNbp($value);
            $rc = $repository->find($this->get('session')->get('id'));
            $event->setIdrecruteur($rc);
            $em=$this->getDoctrine()->getManager();
            $em->persist($event);
            $em->flush();
            $event = $form->getData();
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('noreplay.espritwork@gmail.com')
                ->setTo($event->getEmail())
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        '/front/Event/registration.html.twig',
                        compact('event')
                    ),
                    'text/html'
                )
            ;
            $mailer->send($message);



            return $this->redirectToRoute('afficheevent');
        }
        return $this->render('/front/Event/ajoutE.html.twig',['form'=>$form->createView()]);
    }
    /**
     * @Route("/addrecla", name="addrecla")
     */
    public function addrecla(Request $request,ReclamationRepository $reclamationRepository,RecruteurRepository $recruteurRepository): Response
    {
        $r =$recruteurRepository->find($this->get('session')->get('id'));
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class,$reclamation);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setRecruteur($r);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();
            return $this->redirectToRoute('front');
        }
        return $this->render('/front/newreclamation.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/afficheevent", name="afficheevent")
     */
    public function afficheEvent(EvenementRepository $repository , Request $request , PaginatorInterface $paginator )
    {
        $event=$repository->findAll();
        $event=$repository->OrderByNom();


        $event = $paginator->paginate(

            $event,//on passe les donnees
            $request->query->getInt('page',1),
            4

        );




        return $this->render('/front/Event/afficheE.html.twig',compact('event'));
    }


    /**
     * @Route("/filterevent", name="filterevent")
     */
    public function filterEvent(EvenementRepository $repository , Request $request , PaginatorInterface $paginator )
    {
        $event=$repository->findAll();
        $event=$repository->Cat();


        $event = $paginator->paginate(

            $event,//on passe les donnees
            $request->query->getInt('page',1),
            4

        );
        return $this->render('/front/Event/afficheE.html.twig',compact('event'));
    }






    /**
     * @param $id
     * @param EvenementRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/supprimeevent/{id}" , name="supprimeevent")
     */

    function supprimerEvent($id , EvenementRepository $repository){
        $event=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($event);
        $em->flush();
        return $this->redirectToRoute('afficheevent');
    }

    /**
     * @param EvenementRepository $repository
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @Route("/modifieevent/{id}" , name="modifieevent")
     */

    function modifierEvent(EvenementRepository $repository ,$id , Request $request)

    {

        $event=$repository->find($id);
        $form=$this->createForm(EvenementType::class,$event);
        $form->add('Modifier',SubmitType::class);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('afficheevent');
        }

        return $this->render('/front/Event/modifieE.html.twig' , ['form'=>$form->createView()] );

    }




    /**
     * @Route("/participeevent/{id}", name="participeevent")
     */
    public function participeEvent(EvenementRepository $repository , $id , Request $request)
    {
        $event=$repository->find($id);
        $new_nb=$event->getNbp() + 1;
        $event->setNbp($new_nb);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        $request
            ->getSession()
            ->getFlashBag()
            ->add('participe', ' Votre participation est enregistre avec succes');


        return $this->redirectToRoute('afficheevent');
    }


    /**
     * @param $id
     * @param EvenementRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/likeevent/{id}", name="likeevent")
     */
    public function likeEvent(EvenementRepository $repository , $id )
    {
        $event=$repository->find($id);
        $new=$event->getJaime() + 1;
        $event->setJaime($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('afficheevent');
    }

    /**
     * @param $id
     * @param EvenementRepository $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/dislikeevent/{id}", name="dislikeevent")
     */
    public function dislikeEvent(EvenementRepository $repository , $id )
    {
        $event=$repository->find($id);
        $new=$event->getJaimepas() + 1;
        $event->setJaimepas($new);
        $this->getDoctrine()->getManager()->flush();
        //return $this->render('home/afficheE.html.twig', ['event' => $event]);

        return $this->redirectToRoute('afficheevent');
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
     * @Route("/addfreel", name="addfreel", methods={"GET","POST"})
     */
    public function addfreel(Request $request,RecruteurRepository $repository): Response
    {
        $recruteur = new Recruteur();
        $form = $this->createForm(FreelancerType::class, $recruteur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['photo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $recruteur->setPhoto($filename);
            $recruteur->setType('freelancer');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recruteur);
            $entityManager->flush();

            return $this->redirectToRoute('Login');
        }
        return $this->render('/front/freelancer/freelancer_type.html.twig', [
            'recruteurs' => $repository->findAll(),'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/freelancers",name="freelancers")
     *
     */
    public function freelancersAction(RecruteurRepository $repository){
       // $val = $repository->findAll();
       $recruteur=$repository->findBy(['type'=>'freelancer']);
        return $this->render("front/freelancer/freelancers.html.twig",[
            'recruteurs'=>$recruteur
        ]);
    }
    /**
     * @Route("/projet_index", name="projet_index", methods={"GET"})
     */
    public function projet_index(ProjetRepository $projetRepository): Response
    {
        return $this->render('front/projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }
    /**
     * @Route("/addProjet", name="addProjet", methods={"GET","POST"})
     */
    public function addProjet(Request $request,RecruteurRepository $repository): Response
    {
        $value=$repository->find($this->get('session')->get('id'));
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
        $projet->setUser($value);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['logo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $projet->setLogo($filename);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projet);
            $entityManager->flush();
            return $this->redirectToRoute('projet_index');
        }

        return $this->render('front/projet/_form.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/afficheProjets",name="afficheProjets")
     *
     */
    public function afficheProjets(ProjetRepository $repository){
        $projet=$repository->findAll();
        return $this->render("front/projet/show.html.twig",[
            "projets"=>$projet
        ]);
    }
    /**
     * @Route("/makeoffre", name="makeoffre", methods={"GET"})
     */

    public function makeoffre(Request $request,ProjetRepository $repository,\Swift_Mailer $mailer)
    {
            $projet=$repository->find($request->query->get("id"));
            $message = (new \Swift_Message('Hello Email'))
                ->setFrom('noreplay.espritwork@gmail.com')
                ->setTo($projet->getUser()->getMail())
                ->setBody(
                    "Bonjour l'utilisateur ".$this->get('session')->get('nom')." vous contactez a propos votre offre : ".$projet->getNomProjet()
                );
            $mailer->send($message);

        $sid = 'ACa4b6cca03cde40d5cb4ab9e566958e26';
        $token = '65779274df40f74c45a7a2a745c18020';
          $client = new Client($sid, $token);

          $client->messages->create(
           '+21655215711',
                 [
              'from' => '+18452187055',
              'body' =>               'Bonjour lutilisateur '.$this->get('session')->get('nom').'vous contactez a propos votre offre : '.$projet->getNomProjet()

           ]
          );
        $response = new Response("offre bien ajouter");
        return $response;

    }
    /**
     * @Route("/searchproject", name="searchproject", methods={"GET"})
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
     * @Route("projet_edit/{id}/edit", name="projet_edit", methods={"GET","POST"})
     */
    public function projet_edit(Request $request, Projet $projet): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['logo']->getData();
            $filename = md5(uniqid()).'.'.$uploadedFile->guessExtension();
            $uploadedFile->move($this->getParameter('upload_directory'),$filename);
            $projet->setLogo($filename);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('front/projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("projet_delete/{id}", name="projet_delete", methods={"DELETE"})
     */
    public function projet_delete(Request $request, Projet $projet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projet);
            $entityManager->flush();
        }


        return $this->redirectToRoute('projet_index');

    }
    /**
     * @Route("/calendar", name="calendar_index")
     */
    public function calendar_index(CalendarRepository $repository): Response
    {
        return $this->render('front/calendar/index.html.twig', [
            'calendars' => $repository->findAll(),
        ]);
    }
    /**
     * @Route("/calendar/aff", name="calendar1_index")
     */
    public function calendar1_index(CalendarRepository $calendar): Response
    {
        $events = $calendar->findAll();

        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackground(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }

        $data = json_encode($rdvs);
        return $this->render('front/calendar/aff.html.twig',compact('data'));
    }

    /**
     * @Route("/calendar/new", name="calendar_new", methods={"GET","POST"})
     */
    public function calendar_new(Request $request): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('calendar1_index');
        }

        return $this->render('front/calendar/new.html.twig', [
            'calendars' => $calendar,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/calendar/show/{id}", name="calendar_show", methods={"GET"})
     */
    public function calendar_show(Calendar $calendar): Response
    {
        return $this->render('front/calendar/show.html.twig', [
            'calendars' => $calendar,
        ]);
    }
    /**
     * @Route("/{id}/calendar/edit", name="calendar_edit", methods={"GET","POST"})
     */
    public function calendar_edit(Request $request, Calendar $calendar): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('calendar_index');
        }

        return $this->render('front/calendar/edit.html.twig', [
            'calendars' => $calendar,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/calendar/del/{id}", name="calendar_delete", methods={"DELETE"})
     */
    public function calendar_delete(Request $request, Calendar $calendar): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calendar_index');
    }
    /**
     * @return Response
     * @Route("/front/listT/", name="test_1", methods={"GET"})
     */
    public function test_1(TestRepository $testRepository): Response
    {
        return $this->render('front/test/testT.html.twig', [
            'tests' => $testRepository->findAll(),
        ]);
    }
    /**
     * @Route("/front/obtCertif/{id}", name="obtCertif", methods={"GET","POST"})
     */
    public function obtCertif(Certificat $certificat,CertificatRepository  $repository,$id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $certificat = $entityManager->getRepository(Certificat::class)->find($id);
        $rec = $entityManager->getRepository(Recruteur::class)->find($id);
        $certificat->setIdrecruteur($rec);
        $entityManager->flush();
        $certificat=$repository->findBy(['idrecruteur' => $rec->getId()]);

        return $this->render('front/test/endTest.html.twig',[
            "certificats" => $certificat
        ]);
    }
    /**
     * @Route("/front/listo2/cer/{id}", name="listo2", methods={"GET"})
     */
    public function listo2(CertificatRepository  $certificat,$id): Response
    {
        $p =$certificat->find($id);
        $nom =$p->getNom();
        //$user=$this->getUser()->getUsername();
        //;
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('front/certificat/listo2.html.twig', [
            'certificat' => $p,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @Route("/front/trouver/{id}", name="trouver")
     */
    public function Valider(Request $request,$id,TestRepository $repositorys)
    {
        $test= $repositorys->findBy(
            ['id'=> $id]
        );
        return $this->render('front/test/newFront.html.twig', [
            'tests' => $test,
        ]);//liasion twig avec le controller
    }
    /**
     * @Route("/front/trouver2/{id}", name="trouver2")
     */
    public function Correct(Request $request,$id,TestRepository $repositorys)
    {

        $repository = $this->getDoctrine()->getrepository(Certificat::Class);//recuperer repisotory


        $certificat = $repository->findBy(
            ['test' => $id]
        );
        return $this->render('front/test/congrats.html.twig', [
            'certificats' => $certificat,
        ]);//liasion twig avec le controller

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
            $offre->setIdrecruteur($value);
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
     * @Route("/pos/{id}", name="pos", methods={"GET","POST"})
     */
    public function pos(OffreRepository  $offreRepository,RecruteurRepository $repository,PostulerRepository $postulerRepository,$id): Response
    {
        $pos = $postulerRepository->findBy(['offre'=>$id]);
        return $this->render('/front/membre.html.twig', [
            'postulers' => $pos,
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
            $value=$repository->find($this->get('session')->get('id'));
            $comment->setIdrecruteur($value);
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
     * @Route("/update", name="update", methods={"GET","POST"})
     */
    public function up(OffreRepository  $offreRepository,RecruteurRepository $repository,CommentRepository $commentRepository): Response
    {
        $value=$repository->find($this->get('session')->get('id'));
        $offretype = $offreRepository->findBy(['idrecruteur' => $value]);
        $com = $commentRepository->findBy(['offre'=>$offretype]);
        return $this->render('/front/check.html.twig', [
            'offres' => $offretype,
            'comments'=> $com,
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
     * @Route("/deletecom/{id}",name="deletecommentoffre", methods={"DELETE"})
     */
    public function deletecommentoffre(Request $request,Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('update');
    }
    /**
     * @Route("/deletecomperso/{id}",name="deletecomperso", methods={"DELETE"})
     */
    public function deletecomperso(Request $request,Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
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

