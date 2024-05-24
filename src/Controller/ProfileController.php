<?php

namespace App\Controller;

use App\Dto\Profile as ProfileDto;
use App\Form\ProfileType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/** @method \App\Entity\User getUser() */
#[Route('/profile')]
class ProfileController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }


    /**
     * Route that redirect to the profile of current user
     */
    #[Route('/', name: 'profile_index')]
    public function index(): RedirectResponse
    {
        return $this->redirectToRoute('profile_user', [
            'idUser' => $this->getUser()->getId()
        ]);
    }

    /**
     * route that display and handle a edit profile form
     */
    #[Route('/edit', name: 'profile_edit')]
    public function edit(
        Request $request
    ): Response {
        $user = $this->getUser();
        $dto = new ProfileDto($user);
        $form = $this->createForm(ProfileType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($this->passwordHasher->isPasswordValid($user, $dto->getCurrentPassword())) {
                if ($dto->getNewPassword()) {
                    $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getNewPassword()));
                }
            } else {
                $form->addError(new FormError('Wrong current password'));
            }

            if ($form->isValid()) {
                $user = $this->getUser();
                $user->setUsername($dto->getUsername())
                    ->setEmail($dto->getEmail())
                    ->setPhone($dto->getPhone());

                $address = $user->getAddress();
                $address
                    ->setCity($dto->getCity())
                    ->setPostcode($dto->getPostcode())
                    ->setStreet($dto->getStreet())
                    ->setNumber($dto->getNumber());
                $this->entityManager->persist($user);
                $this->entityManager->persist($address);
                $this->entityManager->flush();
                return $this->redirectToRoute('profile_index');
            }
        }

        return $this->render('profile/edit.html.twig', [
            'title' => 'Edit profile',
            'form' => $form->createView()
        ]);
    }

    /**
     * route that display the profile of a certain user
     */
    #[Route('/{idUser}', name: 'profile_user')]
    public function user(
        int $idUser
    ): Response {
        $user = $this->userRepository->find($idUser);

        return $this->render('profile/index.html.twig', [
            'title' => $user->getUsername(),
            'current_user' => $this->getUser(),
            'user' => $user,
            'address' => $user->getAddress(),
            'posts' => $user->getPosts()
        ]);
    }
}
