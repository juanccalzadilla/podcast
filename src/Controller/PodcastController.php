<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Podcast;
use App\Entity\User;
use App\Form\PodcastType;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class PodcastController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/podcasts', name: 'podcast_index')]
    public function index(): Response
    {
        $user = $this->getUser();
        $user = $this->entityManager->getRepository(User::class)->find($user);

        $podcast = $user->getPodcasts();

        if (!$podcast) {
            return $this->render('podcast/404.html.twig');
        }

        return $this->render('podcast/index.html.twig', [
            'controller_name' => 'PodcastController',
            'podcasts' => $podcast,
            'edit_form' => $this->createForm(PodcastType::class)->createView(),
        ]);
    }

    #[Route('/podcast/{id}', name: 'podcast_show')]
    public function show($id): Response
    {
        $podcast = $this->entityManager->getRepository(Podcast::class)->findPodcast($id);
        $otros = $this->entityManager->getRepository(Podcast::class)->findOtherPodcasts($id);
        dump($podcast);

        if (!$podcast) {
            return $this->render('podcast/404.html.twig');
        }

        return $this->render('podcast/show.html.twig', [
            'controller_name' => 'PodcastController',
            'podcast' => $podcast,
            'otrosPodcasts' => $otros,
        ]);
    }

    #[Route('/podcasts/new', name: 'podcast_new')]
    public function create(Request $request, SluggerInterface $slugger)
    {
        $podcast = new Podcast(visible: true);
        //Usamos el usuario logado
        $user = $this->getUser();
        $podcast->setUser($user);
        $form = $this->createForm(PodcastType::class, $podcast);
        // $podcast->setUser($user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $audio = $form->get('audio')->getData();
            $imagen = $form->get('imagen')->getData();

            if ($audio) {
                $originalFilename = pathinfo($audio->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $audio->guessExtension();

                $audio->move(
                    $this->getParameter('audio_directory'),
                    $newFilename
                );

                $podcast->setAudio($newFilename);
            }

            if ($imagen) {
                $originalFilename = pathinfo($imagen->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imagen->guessExtension();

                $imagen->move(
                    $this->getParameter('image_directory'),
                    $newFilename
                );

                $podcast->setImagen($newFilename);
            }

            $this->entityManager->persist($podcast);
            $this->entityManager->flush();
            return $this->redirectToRoute('podcast_index');
        }

        return $this->render('podcast/create.html.twig', [
            'controller_name' => 'PodcastController',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/podcasts/delete', name: 'podcast_delete', methods: ['POST'])]
    public function delete(Request $request)
    {
        $id = $request->request->get('podcast_id');
        $podcast = $this->entityManager->getRepository(Podcast::class)->find($id);
        $this->entityManager->remove($podcast);
        $this->entityManager->flush();
        return $this->redirectToRoute('podcast_index');
    }


    #[Route('/podcasts/update', name: 'podcast_update')]
    public function update(Request $request, SluggerInterface $slugger)
    {
        $id = $request->request->get('podcast_id');
        $podcast = $this->entityManager->getRepository(Podcast::class)->find($id);
        
        $podcast->setTitulo($request->request->get('titulo'));
        $podcast->setDescripcion($request->request->get('descripcion'));
        $this->entityManager->flush();
        return $this->redirectToRoute('podcast_index');
    }
}
