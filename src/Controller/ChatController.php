<?php

namespace App\Controller;

use App\Dto\Message as MessageDto;
use App\Entity\Chat;
use App\Entity\Message;
use App\Entity\Post;
use App\Form\MessageType;
use App\Repository\ChatRepository;
use App\Repository\PostRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @method \App\Entity\User getUser() */
#[Route('/chat')]
class ChatController extends AbstractController
{
    public function __construct(
        private ChatRepository $chatRepository,
        private MessageRepository $messageRepository,
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * route that show the list of chats
     */
    #[Route('/', name: 'chat_index')]
    public function index(): Response
    {
        return $this->render('chat/index.html.twig', [
            'title' => 'Chats',
            'buyerChats' => $this->getUser()->getBuyerChats(),
            'sellerChats' => $this->getUser()->getSellerChats()
        ]);
    }

    /**
     * route that redirect to corresponding route to chat depending on current user
     */
    #[Route('/post/{idPost}', name: 'chat_post')]
    public function find(
        int $idPost
    ): RedirectResponse {
        $post = $this->postRepository->find($idPost);

        if ($chat = $this->getChatByPost($post)) {
            return $this->redirectToRoute('chat_view', [
                'idChat' => $chat->getId()
            ]);
        } else {
            return $this->redirectToRoute('chat_new', [
                'idPost' => $idPost
            ]);
        }
    }

    /**
     * route that create a new chat and redirect to the chat view
     */
    #[Route('/new/{idPost}', name: 'chat_new')]
    public function new(
        int $idPost
    ): RedirectResponse {
        $post = $this->postRepository->find($idPost);

        $chat = $this->getChatByPost($post);
        if ($chat) {
            return $this->redirectToRoute('chat_view', [
                'idChat' => $chat->getId()
            ]);
        } else {
            $chat = (new Chat())
                ->setBuyer($this->getUser())
                ->setSeller($post->getUser())
                ->setPost($post);
            $this->entityManager->persist($chat);
            $this->entityManager->flush();

            return $this->redirectToRoute('chat_view', [
                'idChat' => $chat->getId()
            ]);
        }
    }

    /**
     * route that send a message into a chat and redirect to chat view
     */
    #[Route('/send', name: 'chat_send')]
    public function send(
        Request $request
    ): RedirectResponse {
        $dto = new MessageDto();
        $this->createForm(MessageType::class, $dto)
            ->handleRequest($request);
        $this->entityManager->persist(
            (new Message())
                ->setContent($dto->getContent())
                ->setChat($dto->getChat())
                ->setSender($this->getUser())
                ->setTimestamp(new \DateTime())
        );
        $this->entityManager->flush();

        return $this->redirectToRoute('chat_view', [
            'idChat' => $dto->getChat()->getId(),
        ]);
    }

    /**
     * route that display a chat view
     */
    #[Route('/{idChat}', name: 'chat_view')]
    public function view(
        int $idChat
    ): Response {
        $chat = $this->chatRepository->find($idChat);

        $form = $this->createForm(MessageType::class, (new MessageDto())->setChat($chat));
        $username = $chat->getPost()->getUser()->getUsername();
        return $this->render('chat/view.html.twig', [
            'title' => "$username (chat)",
            'chat' => $chat,
            'form' => $form->createView()
        ]);
    }

    protected function getChatByPost(
        Post $post
    ): ?Chat {
        $criteria = $this->getUser()->getPosts()->contains($post) ? 'seller' : 'buyer';
        return $this->chatRepository->findOneBy([
            'post' => $post,
            $criteria => $this->getUser()
        ]);
    }
}
