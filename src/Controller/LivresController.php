<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivresType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function PHPUnit\Framework\returnSelf;

#[Route('admin/livres')]
#[IsGranted('ROLE_ADMIN')]
class LivresController extends AbstractController
{
    #[Route('/page/{page<\d+>?1}', name: 'app_livres', methods: ['GET'])]
    public function index(LivresRepository $livresRepository, PaginatorInterface $paginator, Request $request): Response
    {

        $query = $livresRepository->createQueryBuilder('l')->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->attributes->get('page', 1),
            10
        );

        return $this->render('livres/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    //
    // #[Route('/image-good', name: 'app_livres', methods: ['GET'])]
    // public function imagesGood(LivresRepository $livresRepository,EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    // {
    //
    //     $images = array(
    //   "https://m.media-amazon.com/images/I/71++zre30EL._SL1360_.jpg",
    //   "https://m.media-amazon.com/images/I/71sOqrd6JHL._SL1499_.jpg",
    //   "https://m.media-amazon.com/images/I/71HMJiEu7JL._SL1500_.jpg",
    //   "https://m.media-amazon.com/images/I/81lJ9+mcvzL._SL1500_.jpg",
    //   "https://m.media-amazon.com/images/I/71cwqJTWJWL._SL1500_.jpg",
    //   "https://m.media-amazon.com/images/I/61yDxuC-3XL._SL1500_.jpg",
    //     );
    //     $livres = $livresRepository->findAll();
    //     foreach ($livres as $livre) {
    //         $randomImageKey = array_rand($images);
    //         $randomImage = $images[$randomImageKey];
    //         $livre->setImage($randomImage);
    //         $entityManager->persist($livre);
    //     }
    //     $entityManager->flush(); 
    //     // $query = $livresRepository->createQueryBuilder('l')->getQuery();
    //     // $pagination = $paginator->paginate(
    //     //     $query,
    //     //     $request->attributes->get('page', 1),
    //     //     10
    //     // );
    //     return new Response("all good");
    // }

    #[Route('/new', name: 'app_livres_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livres();
        $form = $this->createForm(LivresType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            return $this->redirectToRoute('app_livres', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livres_show', methods: ['GET'])]
    public function show(Livres $livre): Response
    {
        return $this->render('livres/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livres_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivresType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_livres', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livres/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livres_delete', methods: ['POST'])]
    public function delete(Request $request, Livres $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_livres', [], Response::HTTP_SEE_OTHER);
    }
}
