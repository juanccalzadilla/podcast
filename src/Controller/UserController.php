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

        $registroForm = $this->createForm(UserType::class, $user, ['attr' => ['class' => 'w-100']]);
        $registroForm->handleRequest($request);

        if ($registroForm->isSubmitted() && $registroForm->isValid()) {

            $plainPassword = $registroForm->get('password')->getData();
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

            $user->setPassword($hashedPassword);
        
            /**
             * Asignamos el rol de usuario por defecto.
             */
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('podcast_index');
        }

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'registroForm' => $registroForm->createView(),
        ]);
    }

    /**
     * 
     * Metodo para crear un usuario desde el panel de administración
     */

    #[Route('/registroAdmin', name: 'user_registroAdmin', methods: ['POST'])]
    public function createUserByAdmin(Request $request): Response
    {
        $user = new User();

        $user->setNombre($request->request->get('nombre'));
        $user->setApellidos($request->request->get('apellidos'));
        $user->setEmail($request->request->get('email'));

        $plainPassword = $request->request->get('password');
        $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('admin_index');
    }

    #[Route('/logout', name: 'app_logout',)]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


    #[Route('/update', name: 'user_update', methods: ['POST'])]
    public function update(Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($request->request->get('user_id'));

        $user->setNombre($request->request->get('nombre'));
        $user->setApellidos($request->request->get('apellidos'));
        $user->setEmail($request->request->get('email'));

        if ($request->request->get('password') != null) {
            $plainPassword = $request->request->get('password');
            $hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        //Hacemos la redirección a la misma página donde se encontraba el usuario
        if ($user->getRoles() == ['ROLE_ADMIN']) {
            return $this->redirectToRoute('admin_index');
        }

        return $this->redirectToRoute('podcast_index');
    }

    #[Route('/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($request->request->get('user_id'));
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return $this->redirectToRoute('admin_index');
    }
}
