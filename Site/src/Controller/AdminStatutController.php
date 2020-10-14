<?php

namespace App\Controller;

use App\Entity\Statut;
use App\Form\StatutType;
use App\Repository\StatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminStatutController extends AbstractController
{
    /**
     * @Route("/admin/statut/list", name="AdminStatut.index")
     */
    public function index(StatutRepository $statutRepo)
    {

        $statuts =$statutRepo->findAll();

        return $this->render('admin/statut/index.html.twig', [
            'statuts' => $statuts
        ]);
    }

    /**
     * Permet d'ajouter un statut
     * 
     * @Route("/admin/statut/add", name="AdminStatut.add")
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
     */
    public function delete(EntityManagerInterface $em, Statut $statut){

        $libelleStatut = $statut->getLibellestatut();
        
        $em->remove($statut);
        $em->flush();

        $this->addFlash(
            'success',
            "{$libelleStatut} vient d'être supprimé de la liste des statuts."
        );

        return $this->redirectToRoute('AdminStatut.index');
    }
}
