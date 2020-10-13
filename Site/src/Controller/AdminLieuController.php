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
    public function add(Request $request, EntityManagerInterface $em){

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
}
