<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        // Dans le but de simplifier la syntaxe
    }

    #[Route('/inscription', name: 'app_registration')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHash): Response
    {
        $notif = null;
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request); // Valide le formulaire en POST
        if ($form->isSubmitted() && $form->isValid()) { //Si le formulaire est valide
            $user = $form->getData();  //alors je recupere les données de celui-ci
            $search_email = $this->manager->getRepository(User::class)->findOneByEmail($user->getEmail());
            //Cherche un email correspondant dans la bdd
            if (!$search_email) { //Si il n'y a pas d email correspondant
                $password = $passwordHash->hashPassword($user, $user->getPassword()); // Je crypte le password
                $user->setPassword($password); //J'enregiste le password crypté
                $this->manager->persist($user);
                $this->manager->flush();// j'envoie en bdd le contenu du formulaire
                $notif = "Votre inscription s'est correctement déroulée, vous pouvez maintenant vous connecter à votre compte.";
            } else {
                $notif = "L'email que vous avez renseigné existe déjà.";
            }
        }
        return $this->renderForm('registration/index.html.twig', ['form' => $form,"notif" => $notif]);
    }
}
