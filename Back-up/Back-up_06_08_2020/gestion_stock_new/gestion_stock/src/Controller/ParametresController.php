<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Marque;
use App\Entity\Specificite;
use App\Entity\Statut;
use App\Entity\Type;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParametresController extends AbstractController
{
    /*
     * Les paramètres contiennent surtout le CRUD (Create,Read,Update,Delete) pour
     * les lieux,marques, types et spécificité pour les matériels
     */

    /**
     * @Route("/parametre",name="Parametre.index",methods={"GET"})
     * Affiche les paramètres
     */
    public function index(Request $request){
        $session = $request->getSession();
        $session->clear();
        $doctrine = $this->getDoctrine();
        $lieux = $doctrine->getRepository(Lieu::class)->findAll();
        $marques = $doctrine->getRepository(Marque::class)->findAll();
        $specificites = $doctrine->getRepository(Specificite::class)->findAll();
        $types = $doctrine->getRepository(Type::class)->findAll();
        $statuts = $doctrine->getRepository(Statut::class)->findAll();

        return $this->render('parametre/parametre.html.twig',
            [
                'lieux' => $lieux,
                'marques' => $marques,
                'specificites' => $specificites,
                'types' => $types,
                'statuts' => $statuts,
            ]
        );
    }

    /**
     * @Route("/parametre/add/{lib}",name="Parametre.add",methods={"GET","POST"})
     * Permet de rajouter un paramètre
     */
    public function add(Request $request,$lib){
        if ($lib == 'Marque'){
            $obj = new Marque();
            $libelle = 'Marque';
        } elseif ($lib == 'Type'){
            $obj = new Type();
            $libelle = 'Type';
        } elseif ($lib == 'Specificite'){
            $obj = new Specificite();
            $libelle = 'Spe';
        } elseif ($lib == 'Lieu'){
            $obj = new Lieu();
            $libelle = 'Lieu';
        } elseif ($lib == 'Statut'){
            $obj = new Statut();
            $libelle = 'Statut';
        } else {
            return $this->redirectToRoute('Parametre.index');
        }
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$obj);

        $formBuilder
            ->add('libelle'.$libelle,TextType::class)
            ->add('ajouter',SubmitType::class);
        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($obj);
                $em->flush();
                return $this->redirectToRoute('Parametre.index');
            }
        }
        return $this->render('parametre/addParametre.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/parametre/edit/{lib}/{id}",name="Parametre.edit",methods={"GET","POST"})
     * Permet d'editer un paramètres
     */
    public function edit(Request $request,$lib,$id){
        $em = $this->getDoctrine()->getManager();
        if ($lib == 'Marque'){
            $obj = $em->getRepository(Marque::class)->find($id);
            $libelle = 'Marque';
        } elseif ($lib == 'Type'){
            $obj = $em->getRepository(Type::class)->find($id);
            $libelle = 'Type';
        } elseif ($lib == 'Specificite'){
            $obj = $em->getRepository(Specificite::class)->find($id);
            $libelle = 'Spe';
        } elseif ($lib == 'Lieu'){
            $obj = $em->getRepository(Lieu::class)->find($id);
            $libelle = 'Lieu';
        } elseif ($lib == 'Statut'){
            $obj = $em->getRepository(Statut::class)->find($id);
            $libelle = 'Statut';
        } else {
            return $this->redirectToRoute('Parametre.index');
        }

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$obj);

        $formBuilder
            ->add('libelle'.$libelle,TextType::class)
            ->add('modifier',SubmitType::class);
        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($obj);
                $em->flush();
                return $this->redirectToRoute('Parametre.index');
            }
        }
        return $this->render('parametre/editParametre.html.twig',
            [
                'form' => $form->createView(),
            ]
        );

    }

    /**
     * @Route("parametre/delete/{lib}/{id}",name="Parametre.delete",methods={"GET","POST"})
     * Permet de supprimer un paramètre avec avant demande de confirmation
     */
    public function delete(Request $request,$id,$lib){
        $message = '';
        $em = $this->getDoctrine()->getManager();
        if ($lib == 'Marque'){
            $obj = $em->getRepository(Marque::class)->find($id);
        } elseif ($lib == 'Type'){
            $obj = $em->getRepository(Type::class)->find($id);
        } elseif ($lib == 'Specificite'){
            $obj = $em->getRepository(Specificite::class)->find($id);
        } elseif ($lib == 'Lieu'){
            $obj = $em->getRepository(Lieu::class)->find($id);
        } elseif ($lib == 'Statut'){
            $obj = $em->getRepository(Statut::class)->find($id);
        } else {
            return $this->redirectToRoute('Parametre.index');
        }
        $session = $request->getSession();
        $message = $session->get('message');
        return $this->render('parametre/deleteParametre.html.twig',
            [
                'objet' => $obj,
                'libelle' => $lib,
                'identifiant' => $id,
                'message' => $message,
            ]);
    }

    /**
     * @Route("parametre/validateDelete",name="Parametre.validateDelete",methods={"DELETE"})
     * Supprime le parametre en question
     */
    public function validateDelete(Request $request){
        try {
            $em = $this->getDoctrine()->getManager();
            if ($_POST['libelle'] == 'Marque'){
                $obj = $em->getRepository(Marque::class)->find($_POST['id']);
            } elseif ($_POST['libelle'] == 'Type'){
                $obj = $em->getRepository(Type::class)->find($_POST['id']);
            } elseif ($_POST['libelle'] == 'Specificite'){
                $obj = $em->getRepository(Specificite::class)->find($_POST['id']);
            } elseif ($_POST['libelle'] == 'Lieu'){
                $obj = $em->getRepository(Lieu::class)->find($_POST['id']);
            } elseif ($_POST['libelle'] == 'Statut'){
                $obj = $em->getRepository(Statut::class)->find($_POST['id']);
            } else {
                return $this->redirectToRoute('Parametre.index');
            }
            $em->remove($obj);
            $em->flush();
            return $this->redirectToRoute('Parametre.index');
        } catch (ForeignKeyConstraintViolationException $exception){
            $session = $request->getSession();
            $session->set('message','Ce paramètres est utilisé dans un matériel veuillez d\'abord supprimer les matériels en question');
            return $this->redirectToRoute('Parametre.delete',array(
                'id'=>$_POST['id'],
                'lib'=>$_POST['libelle'],
            ));
        }


    }

}