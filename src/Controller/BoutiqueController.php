<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Entity\Panier;
use App\Repository\CategoriesRepository;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    private Security $security;

    #[Route('/boutique/{page<-?\d+>?1}', name: 'app_boutique')]
    public function index(
        LivresRepository $livresRepository,
        CategoriesRepository $categoriesRepository,
        PaginatorInterface $paginator,
        Request $request,
        #[MapQueryParameter] ?string $titre = null,
        #[MapQueryParameter] ?string $auteur = null,
        #[MapQueryParameter] ?string $categorie = null,
    ): Response {
        $queryBuilder = $livresRepository->createQueryBuilder('l')
                                         ->join('l.categorie', 'c');

        if ($titre !== null and $titre !== "") {
            $queryBuilder->andWhere("l.titre LIKE :titre")
                         ->setParameter('titre', '%' . $titre . '%');
        }

        if ($auteur !== null and $auteur !== "") {
            $queryBuilder->andWhere("l.Auteur = :auteur")
                         ->setParameter('auteur', $auteur);
        }

        if ($categorie !== null and $categorie !== "") {
            $queryBuilder->andWhere("c.libelle = :categorie")
                         ->setParameter('categorie', $categorie);
        }

        $query = $queryBuilder->getQuery();

        // var_dump($query->execute());
        $pagination = $paginator->paginate(
            $query,
            $request->attributes->get('page', 1),
            12
        );

        $categories = $categoriesRepository->findAll();
        $livres = $livresRepository->findAll();
        $auteurs = array();

        foreach ($livres as $livre) {
            array_push($auteurs, $livre->getAuteur());
        }
        $filtre = array(
          "titre" => $titre,
          "auteur" => $auteur,
          "categorie" => $categorie,
        );

        return $this->render('boutique/index.html.twig', [
            'pagination' => $pagination,
            'categories' => $categories,
            'auteurs' => $auteurs,
            'filtre' => $filtre,
        ]);
    }

    #[Route('/boutique/livre/{id}', name: 'app_boutique_livre', methods: ['GET'])]
    public function show(Livres $livre): Response
    {
        return $this->render('boutique/livre.html.twig', [
            'livre' => $livre,
        ]);
    }

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

}
