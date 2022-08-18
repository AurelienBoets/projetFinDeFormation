<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        return $this->manager=$manager;
    }
    #[Route('/account/order', name: 'app_account_order')]
    public function index(Request $request): Response
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
        $orders=$this->manager->getRepository(Order::class)->findSuccesOrders($this->getUser());
        return $this->render('account_order/index.html.twig',[
            'form'=>$form->createView(),
            'order'=>$orders
        ]);
    }
    #[Route('/account/order/{reference}', name: 'app_account_order_show')]
    public function show(Request $request,$reference): Response
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
        $order=$this->manager->getRepository(Order::class)->findOneByReference($reference);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('account_order');
        }
        return $this->render('account_order/show.html.twig',[
            'order'=>$order,
            'form'=>$form->createView()
        ]);
    }
}
