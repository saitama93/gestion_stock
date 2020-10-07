<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Marque;
use App\Entity\Specificite;
use App\Entity\Type;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ParametresController extends AbstractController
{
    /**
     * @Route("/parametre",name="Parametre.index",methods={"GET"})
     */
    public function index(){
        $doctrine = $this->getDoctrine();
        $lieux = $doctrine->getRepository(Lieu::class)->findAll();
        $marques = $doctrine->getRepository(Marque::class)->findAll();
        $specificites = $doctrine->getRepository(Specificite::class)->findAll();
        $types = $doctrine->getRepository(Type::class)->findAll();

        return $this->render('parametre/parametre.html.twig',
            [
                'lieux' => $lieux,
                'marques' => $marques,
                'specificites' => $specificites,
                'types' => $types,
            ]
        );
    }

    /**
     * @Route("/parametre/add/{lib}",name="Parametre.add",methods={"GET","POST"})
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
     */
    public function delete(Request $request,$id,$lib){
        $em = $this->getDoctrine()->getManager();
        if ($lib == 'Marque'){
            $obj = $em->getRepository(Marque::class)->find($id);
        } elseif ($lib == 'type'){
            $obj = $em->getRepository(Type::class)->find($id);
        } elseif ($lib == 'Specificite'){
            $obj = $em->getRepository(Specificite::class)->find($id);
        } elseif ($lib == 'Lieu'){
            $obj = $em->getRepository(Lieu::class)->find($id);
        } else {
            return $this->redirectToRoute('Parametre.index');
        }
        return $this->render('parametre/deleteParametre.html.twig',
            [
                'objet' => $obj,
                'libelle' => $lib,
                'identifiant' => $id
            ]);
    }

    /**
     * @Route("parametre/validateDelete",name="Parametre.validateDelete",methods={"DELETE"})
     */
    public function validateDelete(Request $request){
        $em = $this->getDoctrine()->getManager();
        if ($_POST['libelle'] == 'Marque'){
            $obj = $em->getRepository(Marque::class)->find($_POST['id']);
        } elseif ($_POST['libelle'] == 'type'){
            $obj = $em->getRepository(Type::class)->find($_POST['id']);
        } elseif ($_POST['libelle'] == 'Specificite'){
            $obj = $em->getRepository(Specificite::class)->find($_POST['id']);
        } elseif ($_POST['libelle'] == 'Lieu'){
            $obj = $em->getRepository(Lieu::class)->find($_POST['id']);
        } else {
            return $this->redirectToRoute('Parametre.index');
        }
        $em->remove($obj);
        $em->flush();
        return $this->redirectToRoute('Parametre.index');

    }

}