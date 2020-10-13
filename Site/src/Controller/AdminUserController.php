<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="AdminUser.index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepo)
    {
        $users = $userRepo->findAll();

        return $this->render('admin/user/index.html.twig',[
            'users' => $users
        ]);
    }
}
