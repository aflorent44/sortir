<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusFormType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CampusController extends AbstractController
{
    #[Route('/add-campus', name: 'add_campus')]
    public function addCampus(Request $request, EntityManagerInterface $entityManager, CampusRepository $campusRepository): Response
    {
        // Créer une instance du campus
        $campus = new Campus();

        // Créer le formulaire avec l'entité campus
        $form = $this->createForm(CampusFormType::class, $campus);

        // Traiter la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingCampus = $campusRepository->findOneBy(['name' => strtolower($campus->getName())]);

            if ($existingCampus) {
                $this->addFlash('danger', 'Ce campus existe déjà !');
                return $this->redirectToRoute('add_campus');
            } else {
                // Persister et enregistrer dans la base de données
                $entityManager->persist($campus);
                $entityManager->flush();

                // Message de succès et redirection vers la liste des campus
                $this->addFlash('success', 'Campus ajouté avec succès!');
                return $this->redirectToRoute('admin_dashboard_campus');
            }
        }

        // Afficher le formulaire
        return $this->render('campus/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/edit', name: 'app_campus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Campus $campus, EntityManagerInterface $entityManager, CampusRepository $campusRepository): Response
    {
        // Créer et gérer le formulaire
        $form = $this->createForm(CampusFormType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si un autre campus avec le même nom existe (en excluant celui en cours d'édition)
            $existingCampus = $campusRepository->findOneBy(['name' => $campus->getName()]);

            if ($existingCampus && $existingCampus->getId() !== $campus->getId()) {
                // Si un campus avec ce nom existe et que ce n'est pas le même campus que celui que nous éditons
                $this->addFlash('danger', 'Un campus avec ce nom existe déjà !');
                return $this->redirectToRoute('admin_dashboard_campus');
            }

            // Si aucun problème, on enregistre la modification
            $entityManager->flush();
            $this->addFlash('success', 'Campus modifié avec succès !');
            return $this->redirectToRoute('admin_dashboard_campus');
        }

        return $this->render('campus/edit.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

}
