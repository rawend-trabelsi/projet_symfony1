<?php

namespace App\Controller;

use App\Dto\Research as ResearchDto;
use App\Entity\Research as ResearchEntity;
use App\Form\ResearchType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/** @method \App\Entity\User getUser() */
class IndexController extends AbstractController
{
    public function __construct(
        private PostRepository $postRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * route that do a research by handling the search form or do a default research if no form data given
     */
    #[Route('/', name: 'index_index')]
    public function index(
        Request $request
    ): Response {
        $dto = new ResearchDto();
        $researchForm = $this->createForm(ResearchType::class, $dto);
        $researchForm->handleRequest($request);

        $posts = [];

        if ($researchForm->isSubmitted() && $researchForm->isValid()) {
            $posts = $this->postRepository->search(
                $dto->getQuery(),
                $dto->getCategory(),
                $dto->getMinCost(),
                $dto->getMaxCost(),
                $dto->getPostcode()
            );

            if ($this->getUser()) {
                $this->entityManager->persist(
                    (new ResearchEntity())
                        ->setDateTime(new \DateTime())
                        ->setCategory($dto->getCategory())
                        ->setQuery($dto->getQuery())
                        ->setMinPrice($dto->getMinCost())
                        ->setMaxPrice($dto->getMaxCost())
                        ->setPostcode($dto->getPostcode())
                        ->setUser($this->getUser())
                );
                $this->entityManager->flush();
            }
        } else {
            $posts = $this->postRepository->search();
        }

        return $this->render('index/index.html.twig', [
            'title' => 'Index',
            'research_form' => $researchForm->createView(),
            'posts' => $posts,
            'current_user' => $this->getUser(),
        ]);
    }
}
