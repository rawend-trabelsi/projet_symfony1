<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Favorite as FavoriteEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @method \App\Entity\User getUser() */
#[Route('/favorite')]
class FavoriteController extends AbstractController
{
    public function __construct(
        private FavoriteRepository $favoriteRepository,
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * route that display a list of favorite post
     */
    #[Route('/', name: 'favorite_index')]
    public function index(): Response
    {
        return $this->render('favorite/index.html.twig', [
            'title' => 'Favorites',
            'favorites' => $this->favoriteRepository->findByUser(
                $this->getUser(),
                ['createdAt' => 'DESC']
            ),
        ]);
    }

    /**
     * a route that allow to toggle favorite of a post
     */
    #[Route('/toggle/{idPost}', name: 'favorite_toggle')]
    public function toggle(
        int $idPost
    ): RedirectResponse {
        $post = $this->postRepository->find($idPost);

        $favoriteEntity = $this->favoriteRepository->findOneBy(
            [
                'post' => $post,
                'user' => $this->getUser()
            ]
        );

        if ($favoriteEntity) {
            $this->entityManager->remove($favoriteEntity);
        } else {
            $this->entityManager->persist(
                (new FavoriteEntity())
                    ->setPost($post)
                    ->setUser($this->getUser())
                    ->setCreatedAt(new \DateTimeImmutable())
            );
        }
        $this->entityManager->flush();

        return $this->redirectToRoute('post_view', ['idPost' => $idPost]);
    }
}
