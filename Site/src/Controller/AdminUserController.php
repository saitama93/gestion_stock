<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\MailerService;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use App\Service\GeneratePdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher la liste des utilisateurs
     * 
     * @Route("/admin/user/list/{page<\d+>?1}", name="AdminUser.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index($page, PaginationService $paginator)
    {
        $paginator->setEntityClass(User::class)
            ->setCurrentPage($page)
            ->setLimit(10);

        return $this->render('admin/user/index.html.twig', [
            'paginator' => $paginator,
            'presence' => true
        ]);
    }

    /**
     * Permet de créer un utilisateur
     * 
     * @Route("/admin/user/add",name="AdminUser.add",methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer, EntityManagerInterface $em, GeneratePdfService $pdfService, MailerService $mailerService)
    {
        $user = new User();

        $form =  $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $plainPasswd = $user->getPassword();
                $user->setPlainPassword($plainPasswd);
                $password = $passwordEncoder->encodePassword($user, $plainPasswd);
                $user->setPassword($password);
                switch ($_POST['roles']) {
                    case 0:
                        $user->setRoles(array());
                        break;
                    case 1:
                        $user->setRoles(array('ROLE_ADMIN'));
                        break;
                    case 2:
                        $user->setRoles(array('ROLE_PUBLIC'));
                        break;
                    default:
                        $user->setRoles(array());
                        break;
                }
                $user->setPresent(1);
                $em->persist($user);
                $em->flush();
                $this->addFlash('success', 'Compte créé et un mail vous a été envoyé avec vos identifiants');

                //Création et envoie de mail    
                $mailerService->sendMail(
                    'Voici vos informations utilisateurs afin d\'accéder à l\'application',
                    'rononoa.zoro@mugiwara.fr',
                    'igal.ilmiamir@doubs.fr',
                    'Création de compte',
                    'Zoro'
                );

                // GénérationPdfService

                // On renseigne l'entité relié au PDF
                $pdfService->setEntityClass(User::class);

                $pdfService->download('infos_' . $user->getNom(), 'pdf/userInfo.html.twig', [

                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'pseudo' =>  $user->getUsername(),
                    'plainPassword' => $user->getPlainPassword(),
                    'mail' => $user->getMail()
                ]);

                return $this->redirectToRoute('AdminUser.index');
            }
            if ($form->isValid() && $form->isSubmitted()) {

                $this->addFlash('success', 'Utilisateur créé.');
            }
        }
        return $this->render('admin/user/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier les utilisateurs
     * 
     * @Route("admin/user/edit/{id}",name="AdminUser.edit",methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     * 
     */
    public function edit(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, UserRepository $userRepo, GeneratePdfService $pdfService, MailerService $mailerService)
    {
        $user = $userRepo->find($id);
        $check = '';
        $form =  $this->createForm(UserType::class, $user);
        $plainPasswd = $user->getPassword();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $plainPasswd = $user->getPassword();
                $user->setPlainPassword($plainPasswd);
                $password = $passwordEncoder->encodePassword($user, $plainPasswd);
                $user->setPassword($password);

                if ($user->getIduser() != 1 && $user->getIduser() != 2) {
                    switch ($_POST['roles']) {
                        case 0:
                            $user->setRoles(array());
                            break;
                        case 1:
                            $user->setRoles(array('ROLE_ADMIN'));
                            break;
                        case 2:
                            $user->setRoles(array('ROLE_PUBLIC'));
                            break;
                        default:
                            $user->setRoles(array());
                            break;
                    }
                }

                $user->setPresent(1);
                $em->persist($user);
                $em->flush();
                $this->addFlash(
                    'success',
                    "Informations du compte de {$user->getFullName()} modifiées."
                );

                //Création et envoie de mail    

                // sendMail prend en parametres:
                // Le message du mail
                // L'expéditeur du mail
                // Le destinataire du mail
                // L'objet du mail
                // Le nom de l'expéditeur
                $mailerService->sendMail(
                    'Voici vos informations utilisateurs afin d\'accéder à l\'application',
                    'rononoa.zoro@mugiwara.fr',
                    'igal.ilmiamir@doubs.fr',
                    'Création de compte',
                    'Zoro'
                );

                return $this->redirectToRoute('AdminUser.index');
            }
        }
        return $this->render('admin/user/edit.html.twig', [
            'id' => $id,
            'user' => $user,
            'check' => $check,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Permet de supprimer un utilisateur avec page de confirmation
     * 
     * @Route("admin/user/delete/{id}",name="AdminUser.delete",methods={"GET","POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, $id, UserRepository $userRepo, EntityManagerInterface $em)
    {

        $user = $userRepo->find($id);

        if ($request->isMethod('POST')) {
            $user->setPresent(0);
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'danger',
                "Le compte de {$user->getFullName()} a bien été désactivé."
            );

            return $this->redirectToRoute('AdminUser.index');
        }
        return $this->render(
            'user/deleteUser.html.twig',
            [
                'user' => $user,
                'presence' => true
            ]
        );
    }
}
