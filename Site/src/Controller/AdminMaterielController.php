<?php

namespace App\Controller;

use DateTime;
use App\Entity\Materiel;
use App\Form\MaterielType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMaterielController extends AbstractController
{
    /**
     * Permet d'afficher la liste des matériels
     * 
     * @Route("/admin/materiel/list/{page<\d+>?1}", name="AdminMateriel.index")
     */
    public function index($page, PaginationService $paginator)
    {

        $paginator->setEntityClass(Materiel::class)
        ->setCurrentPage($page)
        ->setLimit(10);

        return $this->render('admin/materiel/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'ajouter un materiel
     * 
     * @Route("/admin/materiel/add", name="AdminMateriel.add")
     */
    public function add(EntityManagerInterface $em, Request $request)
    {

        $materiel = new Materiel();

        $form = $this->createForm(MaterielType::class, $materiel);

        $form->handleRequest($request);

        $date = new DateTime('now');

        if ($form->isSubmitted() && $form->isValid() ) {
            $materiel->setNumeroserie(uniqid())
                ->setDate($date)
                ->setSupprimer(false)
                ->setIdUser(null)
                ->setIdlieu(null);

            $em->persist($materiel);
            $em->flush();

            $this->addFlash(
                'success',
                'Matériel créé !'
            );

            return $this->redirectToRoute("AdminMateriel.index");
        }

        return $this->render('admin/materiel/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un matériel
     * 
     * @Route("/admin/materiel/delete/{id}", name="AdminMateriel.delete")
     */
    public function delete(Materiel $materiel, EntityManagerInterface $em){

        $materiel->setSupprimer(true);

        $em->persist($materiel);
        $em->flush();


        $this->addFlash(
            'danger',
            "Matériel supprimé, retrouvé le dans l'onglet 'Archive' !"
        );

        return 
        $this->redirectToRoute('AdminMateriel.index');
    }
}
