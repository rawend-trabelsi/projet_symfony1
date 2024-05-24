<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Dto\Registration as RegistrationDto;
use App\Form\RegistrationFormType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/login', name: 'login_index')]
    public function login(): Response
    {
        return $this->render('login/index.html.twig', [
            'title' => 'Login',
            'error' => $this->authenticationUtils->getLastAuthenticationError(),  // get error
            'last_username' => $this->authenticationUtils->getLastUsername(),     // get last username
        ]);
    }

    #[Route('/logout', name: 'logout_index')]
    public function logout(): void
    {
    }

    #[Route('/register', name: 'register_index')]
    public function register(Request $request): Response
    {
        $registration = new RegistrationDto();
        $form = $this->createForm(RegistrationFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            // handle password equality
            if ($form->get('password')->getData() !== $form->get('confirmPassword')->getData()) {
                $form->get('confirmPassword')->addError(new FormError('The password and its confirmation do not match'));
            }

            if ($form->isValid()) {
                $address = (new Address())
                    ->setPostcode($registration->getPostcode())
                    ->setNumber($registration->getNumber())
                    ->setCity($registration->getCity())
                    ->setStreet($registration->getStreet());
                $this->entityManager->persist($address);

                $user = (new User());
                $user->setEmail($registration->getEmail())
                    ->setUsername($registration->getUsername())
                    ->setPhone($registration->getPhone())
                    ->setPassword(
                        $this->userPasswordHasher->hashPassword(
                            $user,
                            $form->get('password')->getData()
                        )
                    )
                    ->setAddress($address);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $this->redirectToRoute('login_index');
            }
        }

        return $this->render('registration/register.html.twig', [
            'title' => 'Register',
            'registrationForm' => $form->createView(),
        ]);
    }
}
