<?php

namespace App\Controller;

use App\Entity\Commande;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Stripe\Checkout\Session;
use App\Repository\PanierRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CommandeController extends AbstractController
{
    private $security;
    private $entityManager;

    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    // new route to create new commands
    #[Route('/commande/new', name: 'app_commande_new')]
    public function newCommande(): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userPanier = $user->getPanier();
        if (!$userPanier) {
            return $this->redirectToRoute('app_boutique');
        } elseif ($userPanier->getPanierItems()->isEmpty()) {
            return $this->redirectToRoute('app_boutique');
        }

        $commande = (new Commande())
                    ->setUser($user)
                    ->setEtat("en cours")
                    ->setPrix($userPanier->getTotal())
                    ->setDateDeCommande(new DateTime());

        // foreach ($userPanier->getPanierItems() as $item) {
        //     $commande->addCommandeItem($item);
        //     $this->entityManager->remove($item);
        // }
        foreach ($userPanier->getPanierItems() as $item) {
            $achat = $item->toAchat();
            $commande->addAchat($achat);
            $this->entityManager->persist($achat);
        }
        $this->entityManager->persist($commande);

        foreach ($userPanier->getPanierItems() as $item) {
            // $commande->addCommandeItem($item);
            // $this->entityManager->remove($item);
            // $user->getPanier()->getPanierItems()
            // $this->entityManager->persist($user);
            $user->getPanier()->removePanierItem($item);
            $this->entityManager->remove($item);
        }

        $this->entityManager->persist($user->getPanier());
        $this->entityManager->flush();

        return $this->redirectToRoute('app_commande_histoire');
    }

    // #[Route('/{id}', name: 'app_commande')]
    // public function commande(Commande $commande): Response
    // {
    //     return $this->render('commande/commande.html.twig', [
    //         'commande' => $commande,
    //     ]);
    // }

    #[Route('/commande/histoire', name: 'app_commande_histoire')]
    public function histoire(): Response
    {
        $user = $this->security->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('commande/histoire.html.twig', [
            'commandes' => $user->getCommandes(),
        ]);
    }

      #[Route('/commande/details/{id}', name: 'app_commande_details')]
      public function details(Commande $commande): Response
      {
          $user = $this->security->getUser();
          if (!$user) {
              return $this->redirectToRoute('app_login');
          }
          return $this->render('commande/commande.html.twig', [
              'commande' => $commande,
          ]);
      }

    #[Route('/checkout/{id}', name: 'checkout')]
    public function checkouts(Commande $commande): Response
    {
        $total = $commande->getPrix();
        Stripe::setApiKey('sk_test_51PISicP2SnuvfAe7fQ0iZhZ7V5J2i4LbKhgNyhP2WwojK05LRPZ1gvqQf6mMPGUo8OA8JXgqqomTUDgRVoCYwFlV00k8vUVC4l');
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'livres',
                    ],
                    'unit_amount' => $total * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_user', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('app_user', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return $this->redirect($checkout_session->url, 303);
    }
}
