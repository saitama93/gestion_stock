<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Form\StatutType;
use App\Service\PaginationService;
use App\Repository\StatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminStatutController extends AbstractController
{
    /**
     * Permet d'afficher la liste des statuts
     * 
     * @Route("/admin/statut/list/{page<\d+>?1}", name="AdminStatut.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index($page, PaginationService $paginator)
    {

        $paginator->setEntityClass(Statut::class)
        ->setCurrentPage($page)
        ->setLimit(10);

        return $this->render('admin/statut/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'ajouter un statut
     * 
     * @Route("/admin/statut/add", name="AdminStatut.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, EntityManagerInterface $em){

        $statut = new Statut();
        
        $form = $this->createForm(StatutType:: class, $statut);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            
            $em->persist($statut);
            $em->flush();

            $this->addFlash(    
                'success',
                "Le statut {$statut->getLibelleStatut()} a bien été créé. "
            );

            return $this->redirectToRoute('AdminStatut.index');
        }

        return $this->render('admin/statut/add.html.twig',[
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet de modifier un statut
     * 
     * @Route("/admin/statut/edit/{id}", name="AdminStatut.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Statut $statut, Request $request, EntityManagerInterface $em){

        $form = $this->createForm(StatutType::class, $statut);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $em->persist($statut);
            $em->flush();

            $this->addFlash(
                'success',
                "Le statut {$statut->getLibelleStatut()} à bien été modifié."
            );

            return $this->redirectToRoute('AdminStatut.index');
        }

        return $this->render('admin/statut/edit.html.twig', [
            'form' => $form->createView(),
            'statut' => $statut
        ]);
    }


    /**
     * Permet de supprimer un statut
     * 
     * @Route("/admin/statut/delete/{id}", name="AdminStatut.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(EntityManagerInterface $em, Statut $statut){

        $libelleStatut = $statut->getLibellestatut();
        
        $em->remove($statut);
        $em->flush();

        $this->addFlash(
            'danger',
            "{$libelleStatut} vient d'être supprimé de la liste des statuts."
        );

        return $this->redirectToRoute('AdminStatut.index');
    }
}
