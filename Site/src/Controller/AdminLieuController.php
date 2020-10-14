<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminLieuController extends AbstractController
{
    /**
     * @Route("/admin/lieu", name="AdminLieu.index")
     */
    public function index(LieuRepository $lieuRepo)
    {
        // $lieux = $lieuRepo->findBy([], ['libellelieu' => 'ASC'] );
        $lieux = $lieuRepo->findAll();

        return $this->render('admin/lieu/index.html.twig', [
            'lieux' => $lieux,
        ]);
    }

    /**
     * Permet d'ajouter un nouveau lieu
     * 
     * @Route("/admin/lieu/add", name="AdminLieu.add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $lieu = new Lieu();

        $form = $this->createForm(LieuType::class, $lieu);

        $form->handleRequest($request);

        $lieuLibelle = $lieu->getLibellelieu();

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($lieu);
            $em->flush();

            $this->addFlash(
                'success',
                "{$lieuLibelle} a bien été ajouté à la liste"
            );

            return $this->redirectToRoute("AdminLieu.index");
        }

        return $this->render('admin/lieu/add.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * Permet de supprimer un lieu
     * 
     * @Route("/admin/lieu/delete/{id}", name="AdminLieu.delete")
     */
    public function delete(Lieu $lieu, EntityManagerInterface $em)
    {

        $libelleLieu = $lieu->getLibellelieu();

        $em->remove($lieu);
        $em->flush();

        $this->addFlash(
            'success',
            "{$libelleLieu} vient d'être supprimé de la liste des lieux."
        );

        return $this->redirectToRoute('AdminLieu.index');
    }

    /**
     * Permet de modifier un lieu
     * 
     * @Route("/admin/lieu/edit/{id}", name="AdminLieu.edit")
     */
    public function edit(Lieu $lieu, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(LieuType::class, $lieu);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($lieu);
            $em->flush();

            $this->addFlash(
                'success',
                "{$lieu->getLibellelieu()} à bien été modifié."
            );

            return $this->redirectToRoute('AdminLieu.index');
        }

        return $this->render('admin/lieu/edit.html.twig', [
            'form' => $form->createView(),
            'lieu' => $lieu
        ]);
    }
}
