<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminLieuController extends AbstractController
{
    /**
     * Permet d'afficher la liste des lieux
     * 
     * @Route("/admin/lieu/list/{page<\d+>?1}", name="AdminLieu.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index($page, PaginationService $paginator)
    {
        $paginator->setEntityClass(Lieu::class)
            ->setCurrentPage($page)
            ->setLimit(10);


        return $this->render('admin/lieu/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'ajouter un nouveau lieu
     * 
     * @Route("/admin/lieu/add", name="AdminLieu.add")
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
