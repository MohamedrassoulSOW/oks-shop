<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {


            // Vérifier si l'email est déjà pris
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($existingUser) {
                $this->addFlash('error', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }

            // Hacher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Persister l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();

            

            // Rediriger ou afficher un message de succès
            $this->addFlash('success', 'Votre inscription a été réussie.');

            // À mettre en commentaiire en cas d'erreur
            $mail = new Mail();
            $vars = [
                'firstname' => $user->getFirstname()
            ];
            $mail->send(
                $user->getEmail(),
                $user->getFirstname().' '.$user->getLastName(),
                'Bienvenue dans notre boutique OKS-shop',
                'welcome.html',
                $vars
            );

            return $this->redirectToRoute('app_login');  // Ou une autre route de votre choix
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
}
