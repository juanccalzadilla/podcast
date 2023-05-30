<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Podcast;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class PodcastController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/podcast', name: 'podcast_index')]
    public function index(): Response
    {
        $podcast = $this->entityManager->getRepository(Podcast::class)->findAll();

        dump($podcast);
        if (!$podcast) {
            throw $this->createNotFoundException('Podcast no encontrado');
        }

        return $this->render('podcast/index.html.twig', [
            'controller_name' => 'PodcastController',
            'username' => 'Juan Calzadilla',
            'podcasts' => $podcast,
        ]);
    }

    #[Route('/podcast/{id}', name: 'podcast_show')]
    public function show($id): Response
    {
        $podcast = $this->entityManager->getRepository(Podcast::class)->findPodcast($id);
        $otros = $this->entityManager->getRepository(Podcast::class)->findOtherPodcasts($id);
        dump($podcast);

        if (!$podcast) {
            throw $this->createNotFoundException('Podcast no encontrado');
        }
        
        return $this->render('podcast/show.html.twig', [
            'controller_name' => 'PodcastController',
            'username' => 'Juan Calzadilla',
            'podcast' => $podcast,
            'otrosPodcasts' => $otros,
        ]);
    }

    #[Route('/new/podcast', name: 'podcast_new')]
    public function create(){

        $podcast = new Podcast();
        $user = $this->entityManager->getRepository(User::class)->find(1);
        $podcast->setTitulo('Podcast 1')
        ->setDescripcion('Descripcion del podcast 1')
        ->setFechaSubida(new \DateTime('now'))
        ->setAudio('audio.mp3')
        ->setImagen('imagen.jpg')
        ->setVisible(true);

        $podcast->setUser($user);

        $this->entityManager->persist($podcast);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Podcast creado'], Response::HTTP_CREATED);

    }
}
