<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Search;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Entity\Product;
use App\Form\OrderType;
use App\Form\SearchType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        return $this->manager = $manager;
    }
    #[Route('/order', name: 'app_order')]
    public function index(Cart $cart, Request $request): Response
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
        if (empty($cart->getFull())) {
            return $this->redirectToRoute('app_home');
        }
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_address_add');
        }
        $order = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'order' => $order->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    #[Route('/order/recap', name: 'app_order_recap')]
    public function add(Cart $cart, Request $request): Response
    {
        if (empty($cart->getFull())) {
            return $this->redirectToRoute('app_home');
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
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_address_add');
        }
        $order = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        $order->handleRequest($request);
        if ($order->isSubmitted() && $order->isValid()) {
            $date = new DateTime();
            $carriers = $order->get('carriers')->getData();
            $delivery = $order->get('address')->getData();
            $deliveryContent = $delivery->getName();
            $deliveryContent .= '<br>' . $delivery->getPhone();
            if ($delivery->getCompany()) {
                $deliveryContent .= '<br>' . $delivery->getCompany();
            }
            $deliveryContent .= '<br>' . $delivery->getAddress();
            $deliveryContent .= '<br>' . $delivery->getPostal() . '' . $delivery->getCity();
            $deliveryContent .= '<br>' . $delivery->getCountry();
            $recap = new Order();
            $reference = $date->format('dmY') . '-' . uniqid();
            $recap->setReference($reference);
            $recap->setUser($this->getUser());
            $recap->setCreatedAt($date);
            $recap->setCarrierName($carriers->getName());
            $recap->setCarrierPrice($carriers->getPrice());
            $recap->setDelivery($deliveryContent);
            $recap->setState(0);
            $this->manager->persist($recap);
            foreach ($cart->getFull() as $item) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($recap);
                $orderDetails->setProduct($item["product"]->getName());
                $orderDetails->setQuantity($item['quantity']);
                $orderDetails->setPrice($item['product']->getPrice());
                $orderDetails->setTotal($item['product']->getPrice() * $item['quantity']);
                $this->manager->persist($orderDetails);
            }
            $this->manager->flush();
            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $deliveryContent,
                'reference' => $recap->getReference(),
                'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('app_cart');
    }
}
