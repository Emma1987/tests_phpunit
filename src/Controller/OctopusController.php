<?php

namespace App\Controller;

use App\Entity\FunFact;
use App\Form\FunFactType;
use App\Helper\OctopusHelper;
use App\Service\FriendService;
use App\Service\FunFactService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OctopusController extends AbstractController
{
    public function __construct(private FriendService $friendService, private EntityManagerInterface $entityManager) {}

    #[Route('/octopus', name: 'octopus_index')]
    public function index(): Response
    {
        $now = new \DateTimeImmutable();

        return $this->render('octopus/index.html.twig', [
            'isOctopusSleeping' => OctopusHelper::isOctopusSleeping($now),
            'mood' => OctopusHelper::getOctopusMood($now->format('l')),
            'friends' => $this->friendService->getAllMyFriendsAsArray(),
        ]);
    }

    #[Route('/octopus/fun-facts', name: 'octopus_fun_facts')]
    public function funFacts(FunFactService $funFactService): Response
    {
        return $this->render('octopus/fun-facts.html.twig', [
            'funFacts' => $funFactService->findAllFunFactsOrderedByFriendTypeAndContentAsc(),
        ]);
    }

    #[Route('/octopus/add-fun-fact', name: 'octopus_add_fun_fact')]
    public function addFunFact(Request $request): Response
    {
        $funFact = new FunFact();
        $form = $this->createForm(FunFactType::class, $funFact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($funFact);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your fun fact has been saved! 🤓');

            return $this->redirectToRoute('octopus_fun_facts');
        }

        return $this->render('octopus/add-fun-fact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/octopus/delete-fun-fact/{id}', name: 'octopus_delete_fun_fact')]
    public function deleteFunFact(FunFact $funFact): Response
    {
        $this->entityManager->remove($funFact);
        $this->entityManager->flush();

        $this->addFlash('success', 'Your fun fact has been deleted! 🤓');

        return $this->redirectToRoute('octopus_fun_facts');
    }
}
