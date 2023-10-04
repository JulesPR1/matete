<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\User;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AnnonceType;
use App\Repository\CategorieRepository;
use App\Entity\Categorie;
use App\Entity\Emplacement;
use App\Form\CategorieType;
use App\Repository\EmplacementRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Dompdf\Dompdf;
use Dompdf\Options;

use function PHPUnit\Framework\equalTo;

class MateteController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    /**
     * @Route("/", name="carte")
     */
    public function carte(AnnonceRepository $annonce, CategorieRepository $categorie): Response
    {
        return $this->render('matete/carte.html.twig', [
            'annonces' => $annonce->findAll(),
            'categories' => $categorie->findAll()
        ]);
    }

    /**
     * @Route("/lesAnnonces", name="lesAnnonces")
     */
    public function index(Request $request, AnnonceRepository $LesAnnonces, CategorieRepository $lesCategories): Response
    {             
        $page = null;
        $annonces_per_page = 3;
        $annonces = null;

        if($request->query->get('page') != null && intval($request->query->get('page')) > 0){
            $page = intval($request->query->get('page'));
        }else{
            $page = 1;
        }

        $annonces = $LesAnnonces->getPaginatedPosts($page, $annonces_per_page, $request->query->get('recherche'));
        
        //$test_cat = $LesAnnonces->FindByCat('Fruit');
        //dd($test_cat);

        $nb_total_annonces = $LesAnnonces->countAnnonces($request->query->get('recherche'));

        $nb_pages = $nb_total_annonces / $annonces_per_page;
        if ($nb_total_annonces % $annonces_per_page != 0) {
            $nb_pages++;
        }

        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->has('panier') ? $panier = $session->get('panier'): $panier = array();

        return $this->render('matete/index.html.twig', [
            'annonces' => $annonces,
            'panier' => $panier,
            'nb_pages' => $nb_pages,
            'page' => $page,
            'categories' => $lesCategories->findAll(),
            'recherche' => $request->query->get('recherche')
        ]);
    }


     /**
     * @Route("/annonce/{id}", name="annonce_show")
     */
    public function show(AnnonceRepository $annonce, $id): Response
    {   
        $inPanier = false;

        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->has('panier') ? $panier = $session->get('panier'): $panier = array();

        if (array_key_exists($id, $panier))
        {
            $inPanier = true;
        }

        return $this->render('matete/show.html.twig', [
            'annonce' => $annonce->find($id),
            'inPanier' => $inPanier
        ]);
    }

    /**
     * @Route("/nouveau", name="annonce_new")
     */
    public function add(EntityManagerInterface $entityManager, Request $request, CategorieRepository $categorieRepo, EmplacementRepository $emplacementRepo) : Response
    {
        $user = new User();
        //recup user session;
        $user = $this->security->getUser();
        
        $annonce = new Annonce() ;

        $form = $this->createFormBuilder($annonce)
        ->add('nom', TextType::class, ['attr' => ['placeholder' => "Nom", 'class' => 'form-control']])
        ->add('description', TextareaType::class, ['attr' => ['placeholder' => "Description", 'class' => 'form-control mt-3', 'style' => 'resize: none;']])
        ->add('image', FileType::class, ['mapped' => false, 'attr' => ['placeholder' => "Image", 'class' => 'form-control mt-3']])
        ->add('quantite', TextType::class, ['attr' => ['type' => 'number', 'placeholder' => "Quantité (en kg)", 'class' => 'form-control mt-3 quantite']])
        ->add('laCategorie', EntityType::class, ['class' => Categorie::class, 'choices' => $categorieRepo->findAll(), 'attr' => ['class' => 'form-control mt-3']])
        ->add('emplacement', EntityType::class, ['class' => Emplacement::class, 'choices' => $emplacementRepo->findBy(['leUser' => $user->id]), 'attr' => ['class' => 'form-control mt-3']])
        ->getForm();

        $form->handleRequest($request);
        
        //valeur booléenne qui dit si l'utilisateur a des emplacements ou non
        $emplacementEmpty = false;

        if(count($emplacementRepo->findBy(['leUser' => $user->id])) < 1)
        {
            $emplacementEmpty = true;
        }

        if($form->isSubmitted() && $form->isValid()){ //vérif si envoyé et valide
            //récupère l'image uploadée
            $image = $request->files->get('form')['image'];
            //récupère le dossier d'images uploadées
            $uploads_directory = "uploads/public/uploads";
            //créé un nom unique pour l'image
            $filename = md5(uniqid()) . '.' . $image->guessExtension();
            //déplace l'image dans le dossier uploads
            $image->move($uploads_directory, $filename);
            //attribut l'image à l'annonce
            $annonce->setImage($filename);

            //sauvegarde de l’article en bdd
            $time = new \DateTimeImmutable();
            $time->format('H:i:s \O\n d-m-Y');
            $annonce->setCreatedAt($time);
            $annonce->setLeUser($user);
            $entityManager->persist($annonce);
            $entityManager->flush();
            dump($annonce);
            //redirection vers la page de visualisation de l’article
            return $this->redirectToRoute('annonce_show',['id'=>$annonce->getId()]);
        }

        return $this->render('matete/new.html.twig', [
            'emplacementEmpty' => $emplacementEmpty,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="annonce_edit")
     */
    public function edit(EntityManagerInterface $entityManager, AnnonceRepository $annonce, Request $request, $id, CategorieRepository $categorieRepo, EmplacementRepository $emplacementRepo){

        $user = new User();
        //recup user session;
        $user = $this->security->getUser();

        //protection de l'url
        if(!in_array("ROLE_ADMIN", $user->roles)){
            //récupère les ID des annonces de l'utilisateur connecté
            $Id_annonces_User = array();
            if(count($user->lesAnnonces) == 0){
                throw $this->createNotFoundException(
                    "Vous n'avez pas d'annonce à éditer"
                );
            }

            for($i = 0; $i<count($user->lesAnnonces); $i++){
                array_push($Id_annonces_User, $user->lesAnnonces[$i]->getId());
            }

            //compare l'id entré en paramètre avec les ID des annonces de l'utilisateur connecté
            //cela sert à éviter qu'un utilisateur malveillant puisse éditer (via l'url) une annonce qui ne lui appartient pas
            for($i = 0; $i<count($user->lesAnnonces); $i++){
                if(!in_array($id, $Id_annonces_User)){
                    throw $this->createNotFoundException(
                        "Vous n'avez pas accès à l'annonce ayant pour ID : " . $id
                    );
                }
            }
        }
        
        if (!$annonce) {
            throw $this->createNotFoundException(
                "Il n'y a pas d'annonce ayant l'id : " . $id
            );
        }

        //valeur booléenne qui dit si l'utilisateur a des emplacements ou non
        $emplacementEmpty = false;

        if(count($emplacementRepo->findBy(['leUser' => $user->id])) < 1)
        {
            $emplacementEmpty = true;
        }

        $annonce = $annonce->find($id);
        $form = $this->createFormBuilder($annonce)
        ->add('nom', TextType::class, ['attr' => ['placeholder' => "Nom", 'class' => 'form-control']])
        ->add('description', TextareaType::class, ['attr' => ['placeholder' => "Description", 'class' => 'form-control mt-3', 'style' => 'resize: none;']])
        ->add('image', TextType::class, ['attr' => ['placeholder' => "Image", 'class' => 'form-control mt-3']])
        ->add('quantite', TextType::class, ['attr' => ['type' => 'number', 'placeholder' => "Quantité (en kg)", 'class' => 'form-control mt-3 quantite']])
        ->add('laCategorie', EntityType::class, ['class' => Categorie::class, 'choices' => $categorieRepo->findAll(), 'attr' => ['class' => 'form-control mt-3']]);
        
        //donne tous les emplacements à l'admin et limite ceux des utilisateurs classiques aux leurs
        if(in_array("ROLE_ADMIN", $user->roles)){
                $form = $form->add('emplacement', EntityType::class, ['class' => Emplacement::class, 'choices' => $emplacementRepo->findAll(), 'attr' => ['class' => 'form-control mt-3']]);
        }else{
            $form = $form->add('emplacement', EntityType::class, ['class' => Emplacement::class, 'choices' => $emplacementRepo->findBy(['leUser' => $user->id]), 'attr' => ['class' => 'form-control mt-3']]);
        }

        $form = $form->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ //vérif si envoyé et valide
            //sauvegarde de l’article en bdd
            $time = new \DateTimeImmutable();
            $time->format('H:i:s \O\n d-m-Y');
            $annonce->setCreatedAt($time);
            $entityManager->persist($annonce) ;
            $entityManager->flush();
            dump($annonce);
            //redirection vers la page de visualisation de l’article
            return $this->redirectToRoute('annonce_show',['id'=>$annonce->getId()]);
        }
        return $this->render('matete/edit.html.twig', [
            'emplacementEmpty' => $emplacementEmpty,
            'form' => $form->createView(),
            'annonce' => $annonce
        ]);
    }

    /**
     * @Route("/delete/{id}" , name="annonce_delete")
     */
    public function deleteAction(EntityManagerInterface $entityManager, AnnonceRepository $annonce, $id) {

        $user = new User();
        //recup user session;
        $user = $this->security->getUser();

        //protection de l'url
        if(!in_array("ROLE_ADMIN", $user->roles)){
            //récupère les ID des annonces de l'utilisateur connecté
            $Id_annonces_User = array();
            if(count($user->lesAnnonces) == 0){
                throw $this->createNotFoundException(
                    "Vous n'avez pas d'annonce à supprimer"
                );
            }

            for($i = 0; $i<count($user->lesAnnonces); $i++){
                array_push($Id_annonces_User, $user->lesAnnonces[$i]->getId());
            }

            //compare l'id entré en paramètre avec les ID des annonces de l'utilisateur connecté
            //cela sert à éviter qu'un utilisateur malveillant puisse supprimer (via l'url) une annonce qui ne lui appartient pas
            for($i = 0; $i<count($user->lesAnnonces); $i++){
                if(!in_array($id, $Id_annonces_User)){
                    throw $this->createNotFoundException(
                        "Vous n'avez pas accès à l'annonce ayant pour ID : " . $id
                    );
                }
            }
        }

        $annonce = $annonce->find($id);

        if (!$annonce) {
            throw $this->createNotFoundException(
                "Il n'y a pas d'annonce ayant l'id : " . $id
            );
        }

        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->redirectToRoute('lesAnnonces');
    }

    /**
     *    @Route("/ajouter_panier/{id}/{from}", name="ajouter_panier")
     */
    public function addPanier($id, $from, Annonce $annonce) : Response
    {
        $user = new User();
        //recup user session;
        $user = $this->security->getUser();

        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->has('panier') ? $panier = $session->get('panier'): $panier = array();
        array_key_exists($id,$panier)? :$panier[$id]=$annonce;

        //exeption si vous essayez de mettre votre annonce dans le panier
        if($user != null){
            for($i = 0; $i < count($user->lesAnnonces); $i++){
                if($annonce->getId() == $user->lesAnnonces[$i]->getId()){
                    throw $this->createNotFoundException(
                        "Cest votre annonce"
                    );
                }
            }
        }

       // set and get session attributes
        $session->set('panier', $panier);

        //redirige en fonction de la page ou le bouton a été appuyé
        if($from == 'lesAnnonces'){
            return $this->redirectToRoute($from);
        }elseif($from == 'annonce_show'){
            return $this->redirect('/annonce'. "/" .$id);
        }
    }

    /**
    * @Route("/panier", name="panier")
    */
    public function showPanier() : Response
    {
        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->has('panier') ? $panier = $session->get('panier'): $panier = array();

        //faire un findById avec le tableau des id annonces
        $idAnnonces= array_keys($panier);
        $repo= $this->getDoctrine()->getRepository(Annonce::class);
        $annonces = $repo->findBy(['id'=>$idAnnonces]);

        return $this->render('matete/panier.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * @Route("/deletePanier/{id}/{from}" , name="panier_delete")
    */
    public function deletePanier($id, $from) {

        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->has('panier') ? $panier = $session->get('panier'): $panier = array();
        unset($panier[$id]);
        $session->set('panier', $panier);

        if (!$id) {
            throw $this->createNotFoundException(
                "Il n'y a pas d'annonce ayant l'id : " . $id
            );
        }

        //redirige en fonction de la page ou le bouton a été appuyé
        if($from == 'lesAnnonces'){
            return $this->redirectToRoute($from);
        }elseif($from == 'annonce_show'){
            return $this->redirect('/annonce'. "/" .$id);
        }elseif($from == 'panier'){
            return $this->redirect('/panier');
        }
    }

    /**
     * @Route("/pdf", name="pdf")
     */
    public function pdf(AnnonceRepository $annoncesRepo) : Response
    {
        $session = new Session(new NativeSessionStorage(), new AttributeBag());
        $session->has('panier') ? $panier = $session->get('panier'): $panier = array();
        $repo= $this->getDoctrine()->getRepository(Annonce::class);
        $annonces = $repo->findBy(['id'=>$panier]); 
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('matete/pdf.html.twig', [
            'title' => "Votre panier",
            'annonces' => $annonces,
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("matete_panier.pdf", [
            "Attachment" => true
        ]);
        return $this->redirectToRoute('matete/home.html.twig');
    }
}