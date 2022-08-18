<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Classe\Search;
use App\Entity\Order;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeValidateController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        return $this->manager=$manager;
    }
    #[Route('/order/thanks/{CHECKOUT_SESSION_ID}', name: 'app_order_validate')]
    public function index($CHECKOUT_SESSION_ID, Cart $cart,Request $request): Response
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
        $order=$this->manager->getRepository(Order::class)->findOneByStripeSessionId($CHECKOUT_SESSION_ID);
        if(!$order || $order->getUser()!=$this->getUser()){
            return $this->redirectToRoute('app_home');}
            if($order->getState()==0){
                $cart->remove();
                $order->setState(1);
                $this->manager->flush();
                $mail=new Mail();
                $content="Merci pour votre commande.<br/>Lorem ipsum dolor quis veniam autem similique blanditiis, eos assumenda doloribus, perferendis suscipit ratione quae.";
                $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstName(),'Commande',$content);
            }
            
        return $this->render('stripe_validate/index.html.twig',[
            'order'=>$order,
            'form'=>$form->createView()
        ]);
    }
    #[Route('/order/error/{CHECKOUT_SESSION_ID}', name: 'app_order_error')]
    public function error($CHECKOUT_SESSION_ID,Request $request): Response
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
        $order=$this->manager->getRepository(Order::class)->findOneByStripeSessionId($CHECKOUT_SESSION_ID);
        if(!$order || $order->getUser()!=$this->getUser()){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('stripe_validate/error.html.twig',[
            'order'=>$order,
            'form'=>$form->createView()
        ]);
    }
}
