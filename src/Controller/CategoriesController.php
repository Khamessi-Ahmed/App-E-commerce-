<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('admin/categories')]
#[IsGranted('ROLE_ADMIN')]
class CategoriesController extends AbstractController
{
    // #[Route('/boutique/{page<\d+>?1}', name: 'app_boutique')]
    // public function index(LivresRepository $livresRepository, PaginatorInterface $paginator, Request $request): Response
    // {
    //     $query = $livresRepository->createQueryBuilder('l')->getQuery();
    //     $pagination = $paginator->paginate(
    //         $query,
    //         $request->attributes->get('page', 1),
    //         10
    //     );
    //
    //     return $this->render('boutique/index.html.twig', [
    //         'pagination' => $pagination,
    //     ]);
    // }

    #[Route('/page/{page<\d+>?1}', name: 'app_categories', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $categoriesRepository->createQueryBuilder('c')->getQuery();
        $pagination = $paginator->paginate(
            $query,
            $request->attributes->get('page', 1),
            10
        );
        return $this->render('categories/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/new', name: 'app_categories_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Categories();
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_show', methods: ['GET'])]
    public function show(Categories $category): Response
    {
        return $this->render('categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_categories_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoriesType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categories/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_categories_delete', methods: ['POST'])]
    public function delete(Request $request, Categories $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_categories', [], Response::HTTP_SEE_OTHER);
    }
}
