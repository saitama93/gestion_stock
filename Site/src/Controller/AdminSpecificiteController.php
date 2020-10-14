<?php

namespace App\Controller;

use App\Entity\Specificite;
use App\Form\SpecificiteType;
use App\Repository\SpecificiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminSpecificiteController extends AbstractController
{
    /**
     * @Route("/admin/specificite/list", name="AdminSpecificite.index")
     */
    public function index(SpecificiteRepository $specificiteRepo)
    {

        $specificites = $specificiteRepo->findAll();

        return $this->render('admin/specificite/index.html.twig', [
            'specificites' => $specificites
        ]);
    }

    /**
     * Permet d'ajouter une spécificité
     * 
     * @Route("/admin/specificite/add", name="AdminSpecificite.add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {


        $specificite = new Specificite();

        $form = $this->createForm(SpecificiteType::class, $specificite);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($specificite);
            $em->flush();

            $this->addFlash(
                'success',
                "{$specificite->getLibellespe()} a bien été ajouté à la liste"
            );

            return $this->redirectToRoute("AdminSpecificite.index");
        }

        return $this->render('admin/specificite/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une spécificité
     * 
     * @Route("/admin/specificite/edit/{id}", name="AdminSpecificite.edit")
     */
    public function edit(Specificite $specificite, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(SpecificiteType::class, $specificite);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($specificite);
            $em->flush();

            $this->addFlash(
                'success',
                "La spécificité  {$specificite->getLibellespe()} à bien été modifiée."
            );

            return $this->redirectToRoute('AdminSpecificite.index');
        }

        return $this->render('admin/specificite/edit.html.twig',[
            'form' => $form->createView(),
            'specificite' => $specificite
        ]);
    }

    /**
     * Permet de supprimer une spécificité
     * 
     * @Route("/admin/specificite/delete/{id}", name="AdminSpecificite.delete")
     */
    public function delete(EntityManagerInterface $em, Specificite $specificite)
    {

        $libelleSpecificite = $specificite->getLibellespe();

        $em->remove($specificite);
        $em->flush();

        $this->addFlash(
            'danger',
            "{$libelleSpecificite} a bien été supprimé."
        );

        return $this->redirectToRoute('AdminSpecificite.index');
    }
}
