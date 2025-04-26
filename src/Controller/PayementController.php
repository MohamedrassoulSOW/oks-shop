<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PayementController extends AbstractController
{
    /**
     * @Route("/commande/paiement/{id_order}", name="app_payement")
     */
    public function index($id_order, OrderRepository $orderRepository, EntityManagerInterface $entityManager): Response
{
    Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']); // Clé confidentielle à sécuriser en .env

    $order = $orderRepository->findOneBy([
        'id' => $id_order,
        'user' => $this->getUser()
    ]);
    if(!$order){
        return $this->redirectToRoute('app_home');
    }

    $product_for_stripe = [];

    foreach ($order->getOrderDetails() as $product) {
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => number_format($product->getProductPriceWt()*100, 0, '', ''), // Stripe attend un int
                'product_data' => [
                    'name' => $product->getProductName(),
                    'images' => [ // ✅ ici c’est "images" (pluriel)
                        $_ENV['DOMAIN'] . '/uploads/' . $product->getProductIllustration()
                    ],
                ],
            ],
            'quantity' => $product->getProductQuantity(),
        ];
    }

    $product_for_stripe[] = [
        'price_data' => [
            'currency' => 'eur',
            'unit_amount' => number_format($order->getCarrierPrice()*100, 0, '', ''), // Stripe attend un int
            'product_data' => [
                'name' => $order->getCarrierName(),
            ],
        ],
        'quantity' => 1,
    ];

    $checkout_session = Session::create([
        'line_items' => $product_for_stripe, // ✅ pas de double tableau
        'mode' => 'payment',
        'success_url' => $_ENV['DOMAIN'] . '/commande/merci/{CHECKOUT_SESSION_ID}',
        'cancel_url' => $_ENV['DOMAIN'] . '/mon-panier/annulation',
    ]);

    $order->setStripeSessionId($checkout_session->id);
    $entityManager->flush();

    return $this->redirect($checkout_session->url);
}

 /**
     * @Route("/commande/merci/{stripe_session_id}", name="app_payement_success")
     */
    public function success($stripe_session_id, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Cart $cart): Response
{
    $order = $orderRepository->findOneBy([
        'stripe_session_id' => $stripe_session_id,
        'user' => $this->getUser()
    ]);
    if(!$order){
        return $this->redirectToRoute('app_home');
    }
    
    if ($order->getState() == 1) {
        $order->setState(2);
        $cart->remove();
        $entityManager->flush();
    }

    return $this->render('payment/success.html.twig', [
        'order' => $order,
    ]);
}

}