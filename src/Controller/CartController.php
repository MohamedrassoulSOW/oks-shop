<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier/{motif}", name="app_cart", defaults={"motif"=null})
     */
    public function index(Cart $cart, $motif): Response
    {
        If($motif = "annulation"){
            $this->addFlash(
               'info',
               'Paiement annuler : Vous pouvez mettre à jour votre panier et votre commande'
            );
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalwt' => $cart->getTotalwt()
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="app_cart_add")
     */
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        $product = $productRepository->findOneById($id);

        $cart->add($product);

        $this->addFlash(
            'success',
            "Le produit est correctement ajouter dans votre panier"
        );

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/cart/decrease/{id}", name="app_cart_decrease")
     */
    public function decrease($id, Cart $cart): Response
    {
        
        
        $cart->decrease($id);

        $this->addFlash(
            'success',
            "Le produit est correctement retirer dans votre panier"
        );

        return $this->redirectToRoute('app_cart');
    }

    // Vider le panier
    /**
     * @Route("/cart/remove", name="app_cart_remove")
     */
    public function remove(Cart $cart): Response
    {
        $cart->remove();

        return $this->redirectToRoute('app_home');
    }
}
