<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\ChangePasswordType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ChangePasswordController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    #[Route('/account/password', name: 'app_change_password')]
    public function index(Request $request, UserPasswordHasherInterface $hash): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search, ['action' => $this->generateUrl('app_home')]);
        $form->handleRequest($request);
        $products = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->manager->getRepository(Product::class)->findWithSearch($search);
            return $this->render('home/index.html.twig', [
                'form' => $form->createView(),
                'products' => $products
            ]);
        }
        $notification = "";
        $change = $this->createForm(ChangePasswordType::class, $user);
        $change->handleRequest($request);
        if ($change->isSubmitted() && $change->isValid()) {
            $oldpwd = $change->get("oldPassword")->getData();
            if ($hash->isPasswordValid($user, $oldpwd)) {
                $newpwd = $change->get("newPassword")->getData();
                $password = $hash->hashPassword($user, $newpwd);
                $user->setPassword($password);
                $this->manager->flush();
                return $this->redirectToRoute('app_account');
            } else {
                $notification = "Votre mot de passe actuel n'est pas le bon";
            }
        }
        return $this->render('change_password/index.html.twig', [
            'form' => $form->createView(),
            'change' => $change->createView(),
            'notif' => $notification
        ]);
    }
}
