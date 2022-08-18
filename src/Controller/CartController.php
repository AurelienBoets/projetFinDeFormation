<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Form\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request, Cart $cart): Response
    {
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
        return $this->render('cart/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    #[Route('/cart/add/{id}', name: 'app_cart_add')]
    public function add(Cart $cart, $id)
    {
        $cart->add($id);
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/remove', name: 'app_remove_cart')]
    public function remove(Cart $cart)
    {
        $cart->remove();
        return $this->redirectToRoute('app_home');
    }
    #[Route('/cart/delete/{id}', name: 'app_delete_cart')]
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);
        return $this->redirectToRoute('app_cart');
    }
    #[Route('/cart/decrease/{id}', name: 'decrease_to_cart')]
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);
        return $this->redirectToRoute('app_cart');
    }
}
