<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $manager;
    public function __construct(EntityManagerInterface $manager, RequestStack $requestStack)
    {
        $this->manager = $manager;
    }

    #[Route('/', name: 'app_home')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $path = $request->getPathInfo();
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search, ['action' => $this->generateUrl('app_home')]);
        $form->handleRequest($request);
        $products = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->manager->getRepository(Product::class)->findWithSearch($search);
            $products = $paginator->paginate($products, $request->query->getInt('page', 1), limit:10);
            return $this->render('home/index.html.twig', [
                'form' => $form->createView(),
                'products' => $products
            ]);
        }
        if ($path == '/' && !isset($_GET['cat'])) {
            $products = $this->manager->getRepository(Product::class)->findLastProduct();
            $cat = $this->manager->getRepository(Product::class)->findAll();
            $cat = $paginator->paginate($cat, $request->query->getInt('page', 1), limit:10);
            return $this->render('home/index.html.twig', [
            'cat' => $cat,
            'product' => $products,
            'form' => $form->createView()
            ]);
        } elseif ($path = '/' && $_GET['cat'] == 'tec') {
            $cat = $this->manager->getRepository(Product::class)->findByCategory(4);
            $cat = $paginator->paginate($cat, $request->query->getInt('page', 1), limit:10);
            return $this->render('home/index.html.twig', [
            'cat' => $cat,
            'form' => $form->createView()
            ]);
        } elseif ($path = '/' && $_GET['cat'] == 'clothe') {
            $cat = $this->manager->getRepository(Product::class)->findByCategory(1);
            $cat = $paginator->paginate($cat, $request->query->getInt('page', 1), limit:10);
            return $this->render('home/index.html.twig', [
            'cat' => $cat,
            'form' => $form->createView()
            ]);
        } elseif ($path = '/' && $_GET['cat'] == 'other') {
            $cat = $this->manager->getRepository(Product::class)->findByCategory(3);
            $cat = $paginator->paginate($cat, $request->query->getInt('page', 1), limit:10);
            return $this->render('home/index.html.twig', [
            'cat' => $cat,
            'form' => $form->createView()
            ]);
        } elseif ($path = '/' && $_GET['cat'] == 'accessory') {
            $cat = $this->manager->getRepository(Product::class)->findByCategory(2);
            $cat = $paginator->paginate($cat, $request->query->getInt('page', 1), limit:10);
            return $this->render('home/index.html.twig', [
            'cat' => $cat,
            'form' => $form->createView()
            ]);
        }
    }
    #[Route('/produit/{slug}', name: 'app_product')]
    public function show($slug, Request $request): Response
    {
        $product = $this->manager->getRepository(Product::class)->findOneBySlug($slug);
        if (!$product) {
            return $this->redirectToRoute("products");
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
        return $this->render('product/show.html.twig', [
            'form' => $form->createView(),
            "oneProduct" => $product
        ]);
    }
}
