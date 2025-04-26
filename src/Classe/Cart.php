<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function add($product)
    {
        // Appeler la session Symfony
        $session = $this->requestStack->getSession();
        $cart = $this->requestStack->getSession()->get('cart');

        // Vérifie si le panier existe déjà dans la session
        $cart = $session->get('cart', []); // Récupère le panier ou un tableau vide si le panier n'existe pas

        // Vérifie si le produit existe déjà dans le panier
        if (isset($cart[$product->getId()])) {
            // Si le produit est déjà dans le panier, on incrémente la quantité
            $cart[$product->getId()]['qty']++;
        } else {
            // Si le produit n'existe pas dans le panier, on l'ajoute avec une quantité de 1
            if(isset($cart[$product->getId()])) {
                $cart[$product->getId()] = [
                    'objet' => $product,
                    'qty' => $cart[$product->getId()] + 1
                ];
            }else{
                $cart[$product->getId()] = [
                    'objet' => $product,
                    'qty' => 1
                ];
            }
            
        }

        // Enregistrer le panier mis à jour dans la session
        $session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->requestStack->getSession()->get('cart');

        if($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        }else{
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);

    }

    public function fullQuantity()
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $quantity = 0;

        if(!isset($cart)) {
            return $quantity;
        }

        foreach($cart as $product) {
            $quantity += $product['qty'];
        }

        return $quantity;
    }

    public function getTotalwt()
    {
        $cart = $this->requestStack->getSession()->get('cart', []);
        $price = 0;

        if(!isset($cart)) {
            return $price;
        }

        foreach($cart as $product) {
            $price += ($product['objet']->getPrice() * $product['qty']);
        }

        return $price;
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    public function getCart()
    {
        // Retourner le panier depuis la session
        return $this->requestStack->getSession()->get('cart', []);
    }
}



