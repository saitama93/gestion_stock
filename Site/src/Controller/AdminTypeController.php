<?php

namespace App\Controller;

use App\Entity\Type;
use App\Form\TypeType;
use App\Repository\TypeRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminTypeController extends AbstractController
{
    /**
     * Permet d'afficher la liste des statuts
     * 
     * @Route("/admin/type/list/{page<\d+>?1}", name="AdminType.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index($page, PaginationService $paginator)
    {

        $paginator->setEntityClass(Type::class)
            ->setCurrentPage($page)
            ->setLimit(10);

        return $this->render('admin/type/index.html.twig', [
            'paginator' => $paginator
        ]);
    }

    /**
     * Permet de créer un type
     * 
     * @Route("/admin/type/add", name="AdminType.add")
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
     * @IsGranted("ROLE_ADMIN")
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
