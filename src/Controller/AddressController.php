<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Address;
use App\Entity\Product;
use App\Form\AddressType;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    #[Route('/account/address', name: 'app_address')]
    public function index(Request $request): Response
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
        return $this->render('address/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
    #[Route('/account/address/add', name:'app_address_add')]
    public function addAddress(Request $request): Response
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
        $address = new Address();
        $add = $this->createForm(AddressType::class, $address);
        $add->handleRequest($request);
        if ($add->isSubmitted() && $add->isValid()) {
            $address->setUser($user);
            $this->manager->persist($address);
            $this->manager->flush();
            return $this->redirectToRoute('app_address');
        }
        return $this->render('address/add.html.twig', [
            'form' => $form->createView(),
            'add' => $add->createView()
        ]);
    }
    #[Route('/account/delete-address/{id}', name: 'app_address_delete')]
    public function delete($id): Response
    {
        $address = $this->manager->getRepository(Address::class)->findOneById($id);
        if ($address && $address->getUser() == $this->getUser()) {
            $this->manager->remove($address);
            $this->manager->flush();
            return $this->redirectToRoute('app_address');
        }
            return $this->redirectToRoute('app_address');
    }
    #[Route('/account/edit-address/{id}', name:'app_address_edit')]
    public function edit($id, Request $request): Response
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
        $address = $this->manager->getRepository(Address::class)->findOneById($id);
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_address');
        }
        $edit = $this->createForm(AddressType::class, $address);
        $edit->handleRequest($request);
        if ($edit->isSubmitted() && $edit->isValid()) {
            $this->manager->flush();
            return $this->redirectToRoute('app_address');
        }
        return $this->render('address/add.html.twig', [
            'form' => $form->createView(),
            'add' => $edit->createView()
        ]);
    }
}
