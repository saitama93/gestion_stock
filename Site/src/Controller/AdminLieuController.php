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
     * Permet d'afficher la liste des lieux
     * 
     * @Route("/admin/lieu/list/{page<\d+>?1}", name="AdminLieu.index")
     */
    public function index(LieuRepository $lieuRepo, $page)
    {
        // Représente le nombre d'éléments par page
        $limit = 10;

        // 1 * 10 = 10 - 10 = 0
        // 2 * 10 = 20 - 10 = 10
        $start = $page * $limit - $limit;

        // Nombre total d'éléments en BDD
        $total = count($lieuRepo->findAll());

        // Nombre de pages
        // ceil pour arrondir
        $pages = ceil($total / $limit);

        $lieux = $lieuRepo->findBy([], [], $limit, $start);

        return $this->render('admin/lieu/index.html.twig', [
            'lieux' => $lieux,
            'pages' => $pages,
            'page' => $page
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

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($lieu);
            $em->flush();

            $this->addFlash(
                'success',
                "{$lieu->getLibellelieu()} a bien été ajouté à la liste"
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
        $em->remove($lieu);
        $em->flush();

        $this->addFlash(
            'success',
            "{$lieu->getLibellelieu()} vient d'être supprimé de la liste des lieux."
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
                'danger',
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
