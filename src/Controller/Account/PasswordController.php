<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\PasswordUserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/modifier-mot-de-passe", name="app_account_modify_pwd")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
    

        $user = $this->getUser();

        $form = $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            $this->entityManager->flush(); 
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/password/index.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }

}

?>