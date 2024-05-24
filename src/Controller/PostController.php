<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Image;
use App\Form\PostType;
use App\Dto\Post as PostDto;
use App\Repository\FavoriteRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/** @method \App\Entity\User getUser() */
#[Route('/post')]
class PostController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PostRepository $postRepository,
        private FavoriteRepository $favoriteRepository
    ) {
    }

    /**
     * route that display and handle a form to add a Post in database
     */
    #[Route('/', name: 'post_index')]
    public function index(
        Request $request
    ): Response {
        $dto = new PostDto();
        $postForm = $this->createForm(PostType::class, $dto);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $post = (new Post())
                ->setTitle($dto->getTitle())
                ->setCategory($dto->getCategory())
                ->setPrice($dto->getPrice())
                ->setDetail($dto->getDetail())
                ->setPublicationDate(new \DateTime())
                ->setUser($this->getUser());
            $this->entityManager->persist($post);
            $this->entityManager->flush();

            // handle image
            // TODO handle multiple image
            /** @var \Symfony\Component\HttpFoundation\File\File $imageFile */
            if ($imageFile = $postForm->get('image')->getData()) {
                $rank = 0;
                $imageEntity = (new Image())
                    ->setPost($post)
                    ->setRank($rank);
                try {
                    $imageFile->move(
                        __DIR__ . '/../../public/img/posts/',
                        $post->getId() . '-' . $rank
                    );
                    $this->entityManager->persist($imageEntity);
                } catch (FileException $e) {
                    // handle FileException
                }
            }

            $this->entityManager->flush();
            return $this->redirectToRoute('post_view', ['idPost' => $post->getId()]);
        }

        return $this->render('post/index.html.twig', [
            'title' => 'Publish a post',
            'postForm' => $postForm->createView()
        ]);
    }

    /**
     * route that show a post
     */
    #[Route('/view/{idPost}', name: 'post_view')]
    public function view(
        int $idPost
    ): Response {
        $post = $this->postRepository->find($idPost);
        return $this->render('post/view.html.twig', [
            'title' => $post->getTitle(),
            'post' => $post,
            'isOwner' => $this->getUser() == $post->getUser(),
            'isFavorite' => !!$this->favoriteRepository->findOneBy(
                [
                    'post' => $post,
                    'user' => $this->getUser()
                ]
            )
        ]);
    }

    /**
     * route that handle and display a form to edit a post
     */
    #[Route('/edit/{idPost}', name: 'post_edit')]
    public function edit(
        int $idPost,
        Request $request,
    ) {
        $post = $this->postRepository->find($idPost);
        if ($post->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('post_view', ['idPost' => $idPost]);
        } else {
            $dto = (new PostDto())
                ->setTitle($post->getTitle())
                ->setCategory($post->getCategory())
                ->setPrice($post->getPrice())
                ->setDetail($post->getDetail());
            $postForm = $this->createForm(PostType::class, $dto);
            $postForm->handleRequest($request);

            if ($postForm->isSubmitted() && $postForm->isValid()) {
                $post->setTitle($dto->getTitle())
                    ->setCategory($dto->getCategory())
                    ->setPrice($dto->getPrice())
                    ->setDetail($dto->getDetail());
                $this->entityManager->persist($post);
                $this->entityManager->flush();

                return $this->redirectToRoute('post_view', [
                    'idPost' => $post->getId()
                ]);
            } else {
                return $this->render('post/edit.html.twig', [
                    'title' => 'Edit',
                    'postForm' => $postForm->createView(),
                    'post' => $post
                ]);
            }
        }
        return $this->redirectToRoute('post_view', [
            'idPost' => $idPost
        ]);
    }

    /**
     * route that delete a post
     */
    #[Route('/delete/{idPost}', name: 'post_delete')]
    public function delete(
        int $idPost
    ) {
        $post = $this->postRepository->find($idPost);
        if ($post->getUser() !== $this->getUser()) {
            return $this->redirectToRoute('post_view', ['idPost' => $idPost]);
        } else {
            foreach ($post->getChats() as $chat) {
                foreach ($chat->getMessages() as $message) {
                    $this->entityManager->remove($message);
                }
                $this->entityManager->remove($chat);
            }
            foreach ($post->getImages() as $image) {
                $this->entityManager->remove($image);
            }
            foreach ($post->getFavorites() as $favorite) {
                $this->entityManager->remove($favorite);
            }
            $this->entityManager->remove($post);
            $this->entityManager->flush();
            return $this->redirectToRoute('index_index');
        }
    }
}
