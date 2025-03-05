<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\User;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/group')]
#[isGranted('IS_AUTHENTICATED_FULLY')]
final class GroupController extends AbstractController
{
    #[Route(name: 'app_group_index', methods: ['GET'])]
    public function index(GroupRepository $groupRepository): Response
    {
        return $this->render('group/index.html.twig', [
            'groups' => $groupRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_group_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->setOwner($user);
            $entityManager->persist($group);
            $entityManager->flush();

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('group/new.html.twig', [
            'title' => "Créer un groupe",
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_show', methods: ['GET'])]
    public function show(Group $group): Response
    {
        $user = $this->getUser();

        // Check if user is the owner or a member of the group
        if ($user !== $group->getOwner() && !$group->getMembers()->contains($user)) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à voir les détails de ce groupe.');
            return $this->redirectToRoute('app_group_index');
        }

        return $this->render('group/show.html.twig', [
            'group' => $group,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_group_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Check if user is the owner of the group
        if ($user !== $group->getOwner()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce groupe.');
            return $this->redirectToRoute('app_group_index');
        }

        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('group/edit.html.twig', [
            'title' => 'Modifier le groupe "'. $group->getName().'"',
            'group' => $group,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_group_delete', methods: ['POST'])]
    public function delete(Request $request, Group $group, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        // Check if user is the owner of the group
        if ($user !== $group->getOwner()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer ce groupe.');
            return $this->redirectToRoute('app_group_index');
        }

        if ($this->isCsrfTokenValid('delete'.$group->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($group);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_group_index', [], Response::HTTP_SEE_OTHER);
    }
}