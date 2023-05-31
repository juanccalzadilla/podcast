<?php

namespace App\Controller;

use App\Entity\Podcast;
use App\Entity\User;
use App\Form\PodcastType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/admin', name: 'admin_index')]
    public function index(): Response
    {
        /**
         * @var Podcast[] $allPodcasts
         * @var User[] $allUsers
         * @var User $user
         * @var PodcastType $edit_form
         * @var UserType $user_form
         */

        $allPodcasts = $this->entityManager->getRepository(Podcast::class)->findAll();
        $allUsers = $this->entityManager->getRepository(User::class)->findAll();
        $user = new User();
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'podcasts' => $allPodcasts,
            'users' => $allUsers,
            'edit_form' => $this->createForm(PodcastType::class)->createView(),
            'user_form' => $this->createForm(UserType::class, $user)->createView(),
        ]);
    }
}
