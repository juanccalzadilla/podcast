<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/registro', name: 'podcast_userRegistro')]
    public function userRegistro(Request $request): Response
    {
        $user = new User();
        $registroForm = $this->createForm(UserType::class, $user);

        $registroForm->handleRequest($request);

        if ($registroForm->isSubmitted() && $registroForm->isValid()) {

            $plainPassword = $registroForm->get('password')->getData();
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('podcast_index');
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'username' => 'Juan Calzadilla',
            'registroForm' => $registroForm->createView(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}
