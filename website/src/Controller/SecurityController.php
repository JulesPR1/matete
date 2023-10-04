<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Emplacement;
use App\Entity\User;
use App\Form\CategorieType;
use App\Form\EmplacementType;
use App\Form\RegistrationType;
use App\Repository\AnnonceRepository;
use App\Repository\CategorieRepository;
use App\Repository\EmplacementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

class SecurityController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
       $this->security = $security;
    }

    /**
    * @Route("/inscription", name="new_inscription")
    */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $encoder, MailerInterface $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        //récupérer réponse
        $form->handleRequest($request);
        //si formulaire valide, on persiste en bdd
        if($form->isSubmitted()&&$form->isValid()){
            $plaintextPassword = $user->getPassword();
            // hash the password (based on the security.yaml config for the $user class)
            $hashedPassword = $encoder->hashPassword(
                $user,
                $plaintextPassword
            );
            $user->setPassword($hashedPassword);
            $user->addRole("ROLE_USER");
            //token for account validation, will be sent by email
            $user->setToken(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='));
            $user->setEnabled(false);
            $manager->persist($user);
            $manager->flush();
            //mail for account validation
            $email = (new TemplatedEmail())
                ->from('julestestenvoie2@gmail.com')
                ->to(new Address($user->getEmail()))
                ->subject('Matete : Mail de confirmation')

                // path of the Twig template to render
                ->htmlTemplate('emails/signup.html.twig')

                // pass variables (name => value) to the template
                ->context([
                    'username' => $user->getUsername(),
                    'token' => $user->getToken()
                ])
            ;
            $mailer->send($email);
            return $this->redirectToRoute('app_login');
            }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/valider/{token}", name="valider")
     */
    public function valider($token){
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['token' => $token]);
        if($user){
            $user->setEnabled(true);
            $user->setToken(null);
            $this->getDoctrine()->getManager()->flush();
            return $this->render('security/valid.html.twig', [
                'success' => true,
            ]);
        }
        return $this->render('security/valid.html.twig', [
            'success' => false,
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/users", name="list_users")
     */
    public function users(UserRepository $lesUsers): Response
    {
        return $this->render('admin/gestionUsers.html.twig', [
            'users' => $lesUsers->findAll(),
        ]);
    }

    /**
     * @Route("/gestionCategorie", name="gestion_categorie")
     */
    public function gererCategorie(EntityManagerInterface $entityManager, Request $request, CategorieRepository $repo) : Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ //vérif si envoyé et valide
            //sauvegarde de la categorie en bdd
            $entityManager->persist($categorie);
            $entityManager->flush();
            dump($categorie);
            //redirection vers la page de visualisation de l’article
            return $this->redirectToRoute('gestion_categorie');
        }

        return $this->render('admin/gestionCategorie.html.twig', [
            'form' => $form->createView(),
            'categories' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/deleteCategorie/{id}" , name="categorie_delete")
    */
    public function delCategorie(EntityManagerInterface $entityManager, AnnonceRepository $annonceRepo, CategorieRepository $categorie, $id) {

        $categorie = $categorie->find($id);
        $lesAnnonces = $annonceRepo->findby(['laCategorie' => $categorie->getId()]);
        
        for($i = 0; $i < count($lesAnnonces); $i++){
            $entityManager->remove($lesAnnonces[$i]);
        }

        if (!$categorie) {
            throw $this->createNotFoundException(
                "Il n'y a pas de categorie ayant l'id : " . $id
            );
        }

        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->redirectToRoute('gestion_categorie');
    }

    /**
     * @Route("/deleteUser/{id}" , name="user_delete")
    */
    public function delUser(EntityManagerInterface $entityManager, UserRepository $user, $id) {

        $user = $user->find($id);

        $lesEmplacements = $user->lesEmplacements;
        $lesAnnonces = $user->lesAnnonces;

        for($i = 0; $i < count($lesEmplacements); $i++){
            $entityManager->remove($lesEmplacements[$i]);
        }

        for($i = 0; $i < count($lesAnnonces); $i++){
            $entityManager->remove($lesAnnonces[$i]);
        }

        if (!$user) {
            throw $this->createNotFoundException(
                "Il n'y a pas d'utilisateur ayant l'id : " . $id
            );
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('list_users');
    }

    /**
     * @Route("/suspendUser/{id}" , name="user_suspend")
    */
    public function suspendUser(EntityManagerInterface $entityManager, UserRepository $user, $id) {

        $user = $user->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                "Il n'y a pas d'utilisateur ayant l'id : " . $id
            );
        }
        if(!$user->isAdmin()){
            if(!$user->getSuspendu()){
                $user->setSuspendu(true);
            }
            else{
                $user->setSuspendu(false);
            }
        }
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('list_users');
    }

    /**
     * @Route("/showProfil" , name="profil_show")
    */
    public function showProfil(EntityManagerInterface $entityManager, AnnonceRepository $annonceRepo, EmplacementRepository $emplacementRepo, Request $request) 
    {
        $user = new User();
        //recup user session;
        $user = $this->security->getUser();
        $annonces = $user->lesAnnonces;
        $emplacements = $user->lesEmplacements;

        //formulaire emplacement
        $emplacement = new Emplacement() ;
        $form = $this->createForm(EmplacementType::class, $emplacement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){ //vérif si envoyé et valide
            //sauvegarde de l’article en bdd
            $emplacement->setLeUser($user);
            $entityManager->persist($emplacement);
            $entityManager->flush();
            dump($emplacement);
            //redirection vers la page de visualisation de l’article
            return $this->redirectToRoute('profil_show');
        }

        return $this->render('security/showProfil.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
            'emplacements' => $emplacements,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/deleteProfil", name="profil_delete")
     */
    public function deleteProfil(EntityManagerInterface $entityManager): Response
    {
        $user = $this->security->getUser();
        $lesEmplacements = $user->lesEmplacements;
        $lesAnnonces = $user->lesAnnonces;


        //suppr session avant supp bdd
        $session = $this->get('session');
        $session = new Session();
        $session->invalidate();

        for($i = 0; $i < count($lesEmplacements); $i++){
            $entityManager->remove($lesEmplacements[$i]);
        }

        for($i = 0; $i < count($lesAnnonces); $i++){
            $entityManager->remove($lesAnnonces[$i]);
        }
            
        $entityManager->flush();

        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute("app_logout");
    }

     /**
     * @Route("/deleteEmplacement/{id}" , name="emplacement_delete")
    */
    public function delEmplacement(EntityManagerInterface $entityManager, AnnonceRepository $annonceRepo, EmplacementRepository $emplacement, $id) {

        $emplacement = $emplacement->find($id);
        $lesAnnonces = $annonceRepo->findby(['emplacement' => $emplacement->getId()]);
        
        for($i = 0; $i < count($lesAnnonces); $i++){
            $entityManager->remove($lesAnnonces[$i]);
        }

        if (!$emplacement) {
            throw $this->createNotFoundException(
                "Il n'y a pas d'emplacement ayant l'id : " . $id
            );
        }

        $entityManager->remove($emplacement);
        $entityManager->flush();

        return $this->redirectToRoute('profil_show');
    }

    /**
     * @Route("/statistiques", name="stats")
     */
    public function stats(CategorieRepository $repo) : Response
    {
        $categorieLabels = array();
        $categorieDatas = array();
        $categorieColors = array();

        for ($i = 0; $i < count($repo->findAll()); $i++) {
            array_push($categorieLabels, $repo->findAll()[$i]->getNom());
            array_push($categorieDatas, count($repo->findAll()[$i]->getLesAnnonces()));
            array_push($categorieColors, $repo->findAll()[$i]->getColor());
        }

        return $this->render('admin/stats.html.twig', [
            'categorieLabels' => $categorieLabels,
            'categorieDatas' => $categorieDatas,
            'categorieColors' => $categorieColors
        ]);
    }
}
