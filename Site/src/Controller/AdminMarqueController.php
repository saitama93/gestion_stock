<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Form\MarqueType;
use App\Service\PaginationService;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMarqueController extends AbstractController
{
    /**
     * Permet d'afficher la liste des marques
     * 
     * @Route("/admin/marque/list/{page<\d+>?1}", name="AdminMarque.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(MarqueRepository $marqueRepo, $page, PaginationService $paginator)
    {
        $paginator->setEntityClass(Marque::class)
        ->setCurrentPage($page)
        ->setLimit(10);

        return $this->render('admin/marque/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet d'ajouter une marque
     * 
     * @Route("/admin/marque/add", name="AdminMarque.add")
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $marque = new Marque();

        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($marque);
            $em->flush();

            $this->addFlash(
                'success',
                "La marque  {$marque->getLibelleMarque()} a bien été créé. "
            );

            return $this->redirectToRoute('AdminMarque.index');
        }

        return $this->render('admin/marque/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une marque 
     * 
     * @Route("/admin/marque/edit/{id}", name="AdminMarque.edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Marque $marque, Request $request, EntityManagerInterface $em)
    {


        $form = $this->createForm(MarqueType::class, $marque);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($marque);
            $em->flush();

            $this->addFlash(
                'success',
                "La marque {$marque->getLibelleMarque()} à bien été modifié."
            );

            return $this->redirectToRoute('AdminMarque.index');
        }

        return $this->render('admin/marque/edit.html.twig', [
            'form' => $form->createView(),
            'marque' => $marque
        ]);
    }

    /**
     * Permet de supprimer une marque 
     * 
     * @Route("/admin/marque/delete/{id}", name="AdminMarque.delete")
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(EntityManagerInterface $em, Marque $marque)
    {

        $libelleMarque = $marque->getLibellemarque();

        $em->remove($marque);
        $em->flush();

        $this->addFlash(
            'danger',
            "La marque {$libelleMarque} a bien été supprimé"
        );

        return $this->redirectToRoute('AdminMarque.index');
    }
}
