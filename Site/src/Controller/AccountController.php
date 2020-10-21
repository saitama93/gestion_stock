<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet de visualiser le profil de l'utilisateur connecté
     * 
     * @Route("/account/my_account/{id}", name="Account.myAccount")
     * @IsGranted("ROLE_USER")
     */
    public function myAccount(User $user)
    {
        return $this->render('account/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Permet d'éditer un profil
     * 
     * @Route("/account/profile/{id}", name="Account.profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request, EntityManagerInterface $em, User $user){
        
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success', 
                "Les données du profil ont été enregistré avec succès !"
            );

            return $this->redirectToRoute('Account.myAccount');
        }


        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/account/update-password", name="Account.updatePassword")
     *  @IsGranted("ROLE_USER")
     */
    public function updatePassword(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {

        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $oldPassword = $passwordUpdate->getoldPassword();

            // dd($form->get('oldPassword'));

            // Vérifions si le oldPassword du formulaire soit le même que le password du user s
            if (!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())) {
                // Gérer l'erreur
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel !"));
            } else {

                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setPassword($hash);

                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );
                return $this->redirectToRoute('Home.index');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
