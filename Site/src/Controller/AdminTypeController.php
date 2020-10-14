<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminTypeController extends AbstractController
{
    /**
     * @Route("/admin/type", name="AdminType.index")
     */
    public function index(TypeRepository $typeRepo)
    {

        $types = $typeRepo->findAll();

        return $this->render('admin/type/index.html.twig', [
            'types' => $types
        ]);
    }

    /**
     * Permet de créer un type
     * 
     * @Route("/admin/type/add", name="AdminType.add")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {

        $type = new Type();

        $form = $this->createForm(TypeType::class, $type);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($type);
            $em->flush();

            $this->addFlash(
                'success',
                "Le type  {$type->getLibelleType()} a bien été créé. "
            );

            return $this->redirectToRoute('AdminType.index');
        }

        return $this->render('admin/type/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier un type
     * 
     * @Route("/admin/type/edit/{id}", name="AdminType.edit")
     */
    public function edit(Type $type, Request $request, EntityManagerInterface $em)
    {

        $form = $this->createForm(TypeType::class, $type);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($type);
            $em->flush();

            $this->addFlash(
                'success',
                "Le type  {$type->getLibelleType()} a bien été modifié. "
            );

            return $this->redirectToRoute('AdminType.index');
        }

        return $this->render('admin/type/edit.html.twig',[
            'form' => $form->createView(),
            'type' => $type
        ]);
    }

    /**
     * Permet de supprimer un type
     * 
     * @Route("/admin/type/delete/{id}", name="AdminType.delete")
     */
    public function delete(EntityManagerInterface $em, Type $type)
    {

        $libelleType = $type->getLibelletype();

        $em->remove($type);
        $em->flush();

        $this->addFlash(
            'danger',
            "Le type {$libelleType} a bien été supprimé."
        );

        return $this->redirectToRoute('AdminType.index');
    }
}
