<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="User.index",methods={"GET"})
     * Permet d'afficher tous les utilisateurs
     */
    public function index(Request $request){
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository(User::class)->findAll();
        return $this->render('user/index.html.twig',[
            'users' => $users,
        ]);
    }

    /**
     * @Route("/user/add",name="User.add",methods={"GET","POST"})
     * Permet de créer un utilisateur
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder){
        $message = '';
        $user = new User();
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$user);
        $formBuilder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('mail',TextType::class)
            ->add('username',TextType::class)
            ->add('password',PasswordType::class)
            ->add('ajouter',SubmitType::class);
        $form = $formBuilder->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()){
                try {
                    $plainPasswd = $user->getPassword();
                    $user->setPlainPassword($plainPasswd);
                    $password = $passwordEncoder->encodePassword($user,$plainPasswd);
                    $user->setPassword($password);
                    if (isset($_POST['admin'])){
                        $user->setRoles(array('ROLE_ADMIN'));
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'User creer');

                    return $this->redirectToRoute('User.index');
                } catch (UniqueConstraintViolationException $exception){
                    $message = 'Cet utilisateur existe déjà !';
                }
            }

        }
        return $this->render('user/addUser.html.twig',[
            'message'=>$message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("user/edit/{id}",name="User.edit",methods={"GET","POST"})
     * Permet de modifier les utilisateurs
     */
    public function edit(Request $request, $id,UserPasswordEncoderInterface $passwordEncoder){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $check = '';
        $message = '';
        foreach ($user->getRoles() as $role){
            if ($role == "ROLE_ADMIN"){
                $check = 'checked';
            }
        }
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class,$user);
        $formBuilder
            ->add('idUser',HiddenType::class)
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('mail',TextType::class)
            ->add('username',TextType::class)
            ->add('password',PasswordType::class,[
                'always_empty' => true,
            ])
            ->add('ajouter',SubmitType::class);
        $form = $formBuilder->getForm();
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()){
                try {
                    $plainPasswd = $user->getPassword();
                    $user->setPlainPassword($plainPasswd);
                    $password = $passwordEncoder->encodePassword($user,$plainPasswd);
                    $user->setPassword($password);
                    if (isset($_POST['admin'])){
                        $user->setRoles(array('ROLE_ADMIN'));
                    } else {
                        $user->setRoles(array());
                    }
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $request->getSession()->getFlashBag()->add('notice', 'User creer');

                    return $this->redirectToRoute('User.index');
                } catch (UniqueConstraintViolationException $exception){
                    $message = 'Cet utilisateur existe déjà !';
                }
            }

        }
        return $this->render('user/editUser.html.twig',[
            'user' => $user,
            'check' => $check,
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("user/delete/{id}",name="User.delete",methods={"GET","POST"})
     * Permet de supprimer un utilisateur avec page de confirmation
     */
    public function delete(Request $request,$id){
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        if ($request->isMethod('POST')){
            $em->remove($user);
            $em->flush();
            return $this->redirectToRoute('User.index');
        }
        return $this->render('user/deleteUser.html.twig',
            [
                'user' => $user
            ]);
    }
}