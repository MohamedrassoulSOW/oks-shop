<?php

namespace App\Controller\Account;

use App\Classe\Cart;
use App\Entity\Address;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;

class AddressController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @Route("/compte/adresses", name="app_account_addresses")
     */
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }

    /**
     * @Route("/compte/adresses/delete/{id}", name="app_account_address_delete")
     */
    public function Delete($id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);
        if(!$address || $address->getUser() != $this->getUser()){
        return $this->redirectToRoute('app_account_addresses');
        }
        $this->addFlash(
            'success',
            'Votre adresse est correctement Supprimer.'
        );
        $this->entityManager->remove($address);
        $this->entityManager->flush();
        
        return $this->redirectToRoute('app_account_addresses');
    }

    /**
     * @Route("/compte/adresse/ajouter/{id}", name="app_account_address_form")
     */
    public function Form(Request $request, AddressRepository $addressRepository, Cart $cart, $id = null): Response
    {
        if($id){
            $address = $addressRepository->findOneById($id);
            if(!$address || $address->getUser() != $this->getUser()){
                return $this->redirectToRoute('app_account_addresses');
            }
        }else{
            $address = new Address();
            $address->setUser($this->getUser());
        }

        $form = $this->createForm(AddressUserType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->persist($address);
            $this->entityManager->flush(); 

            $this->addFlash(
                'success',
                'Votre adresse est correctement sauvegardée.'
            );

            if($cart->fullQuantity() > 0){
                return $this->redirectToRoute('app_orde');
            }

            return $this->redirectToRoute('app_account_addresses');
        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form->createView() // ✅ Correct
        ]);
    }
}


?>