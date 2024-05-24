<?php

namespace App\Controller;

use App\Form\ResearchType;
use App\Repository\ResearchRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;

#[Route('/research')]
class ResearchController extends AbstractController
{
    public function __construct(
        private ResearchRepository $researchRepository
    ) {
    }

    /**
     * route that display current user's research history
     */
    #[Route('/', name: 'research_index')]
    public function index(): Response
    {
        return $this->render('research/index.html.twig', [
            'title' => 'Research',
            'researches' => $this->researchRepository->findByUser($this->getUser(), ['dateTime' => 'DESC']),
            'formObject' => $this->createForm(ResearchType::class),
        ]);
    }

    /**
     * route that clear the current user's history and redirect to research_index
     */
    #[Route('/clear', name: 'research_clear')]
    public function clear(): RedirectResponse
    {
        $this->researchRepository->clearHistoryForUser($this->getUser());
        return $this->redirectToRoute('research_index');
    }
}
