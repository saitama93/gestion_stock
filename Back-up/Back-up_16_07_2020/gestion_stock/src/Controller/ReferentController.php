<?php

namespace App\Controller;

use App\Entity\Referent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReferentController extends AbstractController
{
    /**
     * @Route("/referent",name="Referent.index",methods={"GET"})
     */
    public function index(){
        $repository = $this->getDoctrine()->getRepository(Referent::class);
        $referents = $repository->findBy(array('present'=>1));

        return $this->render('referent/referent.html.twig',
            [
                'referents' => $referents,
            ]
        );
    }

    /**
     * @Route("/referent/add",name="Referent.add",methods={"GET","POST"})
     */
    public function add(Request $request){
        $referent = new Referent();
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$referent);
        $formBuilder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('equipe',TextType::class)
            ->add('mailEquipe',TextType::class)
            ->add('ajouter',SubmitType::class);
        $form = $formBuilder->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($referent);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Referent bien créer');
                return $this->redirectToRoute('Referent.edit',array('id'=>$referent->getIdreferent()));
            }
        }
        return $this->render('referent/addReferent.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * @Route("referent/edit/{id}",name="Referent.edit",methods={"GET","POST"})
     */
    public function edit(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $referent = $em->getRepository(Referent::class)->find($id);
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$referent);
        $formBuilder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('equipe',TextType::class)
            ->add('mailEquipe',TextType::class)
            ->add('modifier',SubmitType::class);
        $form = $formBuilder->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($referent);
                $em->flush();
                $request->getSession()->getFlashBag()->add('notice', 'Referent bien créer');
                return $this->redirectToRoute('Referent.index');
            }
        }
        return $this->render('referent/editReferent.html.twig',
            [
                'form' => $form->createView(),
                'id' => $id,
            ]
        );
    }

    /**
     * @Route("referent/delete/{id}",name="Referent.delete",methods={"GET","POST"})
     */
    public function delete(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $referent = $em->getRepository(Referent::class)->find($id);
        if ($request->isMethod('POST')){
            $referent->setPresent(0);
            $em->persist($referent);
            $em->flush();
            return $this->redirectToRoute('Referent.index');
        }
        return $this->render('referent/deleteReferent.html.twig',
        [
            'referent' => $referent
        ]);
    }
}