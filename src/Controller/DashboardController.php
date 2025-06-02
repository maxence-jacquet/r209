<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Etat;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/dashboardAdmin', name: 'app_dashboard_admin')]
    public function dashboardAdmin(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Notes "En cours" avec QueryBuilder
        $notesEnCours = $em->createQueryBuilder()
            ->select('n')
            ->from(Note::class, 'n')
            ->join('n.etat', 'e')
            ->where('e.nom = :etat')
            ->setParameter('etat', 'En cours')
            ->getQuery()
            ->getResult();

        // Notes urgentes (avec tag "Urgent")
        $notesUrgentes = $em->createQueryBuilder()
            ->select('n')
            ->from(Note::class, 'n')
            ->join('n.tag', 't')
            ->where('t.nom = :tag')
            ->setParameter('tag', 'Urgent')
            ->getQuery()
            ->getResult();

        // Toutes les notes de l'utilisateur courant
        $notes = $user->getNotes();

        // Notes "En cours" filtrées avec Criteria
        $etatEnCours = $em->getRepository(Etat::class)->findOneBy(['nom' => 'En cours']);
        $criteriaEtat = Criteria::create()->where(Criteria::expr()->eq('etat', $etatEnCours));
        $notesEnCours2 = $notes->matching($criteriaEtat);

        return $this->render('dashboard/admin.html.twig', [
            'notes_en_cours' => $notesEnCours,
            'notes_en_cours2' => $notesEnCours2,
            'notes_urgentes' => $notesUrgentes,
            'notes_utilisateur' => $notes,
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboardUser(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $notes = $em->getRepository(Note::class)->findBy(['user' => $user]);

        return $this->render('dashboard/exemple.html.twig', [
            'notes' => $notes,
        ]);
    }
}
