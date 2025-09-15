<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Quote;
use App\Form\ContactType;
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
    public function index(EntityManagerInterface $manager): Response
    {
        $products = $manager->getRepository(Product::class)->findBy([], [], 3);
        return $this->render('main/index.html.twig', [
            'products' => $products
        ]);
    }

    #[Route('/about-us', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('main/about.html.twig', [

        ]);
    }

    #[Route('/products', name: 'app_products')]
    public function products(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Récupération de tous les produits (ou d'une query builder si besoin de filtres)
        $query = $productRepository->createQueryBuilder('p')
            ->orderBy('p.id', 'DESC') // exemple : ordre par id décroissant
            ->getQuery();

        // Pagination
        $products = $paginator->paginate(
            $query,                       // Requête ou tableau
            $request->query->getInt('page', 1), // Page courante
            12                          // Produits par page
        );


        return $this->render('main/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_detail')]
    public function detail(Product $product): Response
    {
        return $this->render('product/detail.html.twig', [
            'product' => $product,
        ]);
    }


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
    public function contact(Request $request, EntityManagerInterface $em): Response
    {


        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $contact->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($contact);
            $em->flush();

            $this->addFlash('success', 'Votre message a bien été envoyé !');

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('main/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
