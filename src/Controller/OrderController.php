<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/commande/livraison", name="app_orde")
     */
    public function index(): Response
    {
        $addresses = $this->getUser()->getAddresses();

        if(count($addresses) == 0){
            return $this->redirectToRoute('app_account_address_form');
        }

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses, // ✅ CORRECT
            'action' => $this->generateUrl('app_sumary')
        ]);
        

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/commande/recapitilatif", name="app_sumary")
     */
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        $products = $cart->getCart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses()
        ]);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $addressObj = $form->get('addresses')->getData();
            $address = $addressObj->getFirstName().' '.$addressObj->getLastName().'</br>';
            $address .= $addressObj->getAddress().'</br>';
            $address .= $addressObj->getPostal().' '.$addressObj->getCity().'</br>';
            $address .= $addressObj->getCountry().'</br>';
            $address .= $addressObj->getPhone().'</br>';
            
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            foreach($products as $product){
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['objet']->getName());
                $orderDetail->setProductIllustration($product['objet']->getIllustration());
                $orderDetail->setProductPrice($product['objet']->getPrice());
                $orderDetail->setProductTva($product['objet']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();
    
        }

        return $this->render('order/sumary.html.twig', [
            'choices' =>$form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalwt' => $cart->getTotalwt()
        ]);
    }

}
