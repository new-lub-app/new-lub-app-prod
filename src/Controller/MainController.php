<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Quote;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

final class MainController extends AbstractController
{
    #[Route('', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [

        ]);
    }

    #[Route('/about-us', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('main/about.html.twig', [

        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function products(
        Request                $request,
        EntityManagerInterface $manager,
        ProductRepository      $productRepository,
        PaginatorInterface     $paginator
    ): Response
    {
        // Récupérer les paramètres de filtrage
//        $search = $request->query->get('search');
//        $categories = $request->query->get('categories', []);
//        $priceMax = $request->query->get('price_max', 1000);
//        $sort = $request->query->get('sort', 'relevance');
//
//        // Construire la requête avec les filtres
//        $query = $productRepository->createQueryBuilder('p')
//            ->where('p.isActive = true');
//
//        // Filtre par recherche
//        if ($search) {
//            $query->andWhere('p.name LIKE :search OR p.description LIKE :search')
//                ->setParameter('search', '%' . $search . '%');
//        }
//
//        // Filtre par catégories
//        if (!empty($categories)) {
//            $query->join('p.categories', 'c')
//                ->andWhere('c.id IN (:categories)')
//                ->setParameter('categories', $categories);
//        }
//
//        // Filtre par prix
//        $query->andWhere('p.price <= :price_max')
//            ->setParameter('price_max', $priceMax);
//
//        // Tri des résultats
//        switch ($sort) {
//            case 'price_asc':
//                $query->orderBy('p.price', 'ASC');
//                break;
//            case 'price_desc':
//                $query->orderBy('p.price', 'DESC');
//                break;
//            case 'name_asc':
//                $query->orderBy('p.name', 'ASC');
//                break;
//            case 'name_desc':
//                $query->orderBy('p.name', 'DESC');
//                break;
//            default:
//                $query->orderBy('p.createdAt', 'DESC');
//                break;
//        }

        // Pagination
//        $products = $paginator->paginate(
//            $query->getQuery(),
//            $request->query->getInt('page', 1),
//            12 // Nombre d'éléments par page
//        );

        // Récupérer toutes les catégories pour les filtres
//        $categories = $this->getDoctrine()
//            ->getRepository(Category::class)
//            ->findAll();
        $products = $manager->getRepository(Product::class)->findAll();

        return $this->render('main/products.html.twig', [
            'products' => $products,
            'categories' => [],
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_detail')]
    public function detail(Product $product): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product,
        ]);
    }

//    #[Route('/products', name: 'app_products')]
//    public function products(EntityManagerInterface $manager): Response
//    {
//        $products = $manager->getRepository(Product::class)->findAll();
//        return $this->render('main/products.html.twig', ['products' => $products]);
//    }

    #[Route('/request-quote/{id}', name: 'app_request_quote')]
    public function request_quote(Product $product, Request $request, EntityManagerInterface $manager): Response
    {
        if (!$product) return $this->redirectToRoute('app_products');
        if ($request->getMethod() == 'POST') {
            $quote = new Quote();
            $quote->setFirstname($request->get('firstname', ''));
            $quote->setLastname($request->get('lastname', ''));
            $quote->setEmail($request->get('email', ''));
            $quote->setPhoneNumber($request->get('phoneNumber', ''));
            $quote->setMessage($request->get('message', ''));
            $quote->setPorduct($product);
            $quote->setUpdatedAt(new \DateTimeImmutable());
            $quote->setStatus('0');
            $manager->persist($quote);
            $manager->flush();
            $this->addFlash('success', '   Votre devis sera traité dans les 24 heures. Un conseiller vous contactera pour finaliser votre commande.');
            return $this->redirectToRoute('app_products');
        }
        return $this->render('main/request_quote.html.twig', ['product' => $product]);
    }


    #[Route('/contact-us', name: 'app_contact')]
    public function contact(Request $request, EntityManagerInterface $manager): Response
    {

        if ($request->getMethod() == 'POST') {
            dd($request);
            $this->addFlash('success', '   Votre devis sera traité dans les 24 heures. Un conseiller vous contactera pour finaliser votre commande.');
            return $this->redirectToRoute('app_products');
        }
        return $this->render('main/contact.html.twig',);
    }


}
