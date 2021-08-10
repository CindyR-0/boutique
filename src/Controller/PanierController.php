<?php

namespace App\Controller;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier_index")
     */
    public function index(SessionInterface $sessionInterface): Response
    {
        // 1. On récupère le panier s'il existe, sinon on prend un nouveau
        $cart = $sessionInterface->get('cart');
        if ($cart === null){
            $cart= [
                'total' =>0.0,
                'infoProduits' => []
            ];
        }

        return $this->render('panier/index.html.twig', [
            'cart' => $cart
        ]);
    }

    //ajouter 1 livre dans le panier
    /**
     * @Route("/panier/ajouter/{id}", name="panier_add")
     */
    public function add(Book $book, SessionInterface $sessionInterface): Response
    { //session interface permet la recuperation du panier dans l'interface
        // $cart = $sessionInterface->get('cart', [
        //     'total' => 0.0,
        //     'infoProduits' => []
        // ]);//récuperation du panier de la session

        // 1. On récupère le panier s'il existe, sinon on prend un nouveau
        $cart = $sessionInterface->get('cart');
        if ($cart === null){
            $cart= [
                'total' =>0.0,
                'infoProduits' => []
            ];
        }
        // plus besoin du foreach avec le tableau associatif
        // //on parcours l'ensemble des infos dans le panier
        // foreach($cart['infoProduits'] as $element) {
        //     //si l'info se trouve être le produit à ajouter 
        //     if($element['book']->getId() === $book->getId()){
        //         //on incrémenter la quantité
        //         ++$element['quantity'];
        //         //on met  a jour le total du panier
        //         $cart['total'] = $cart['total'] + $book->getPrice();
        //     }
        // }
        
        // 2. On ajoute le book s'il n'y en a pas
        $bookId = $book->getId();
        if (!isset($cart['elements'][$bookId])){ //si le bookId n'existe pas dans elements dans cart je l'ajoute dans le panier (id = clé element)
             
            $cart['elements'][$bookId] = [
                'book' => $book,
                'quantity' => 0
            ];
        }

        // 3. On incrémente la quantityé est on recalcule le prix total
        $cart['elements'][$bookId]['quantity'] = $cart['elements'][$bookId]['quantity'] + 1; //ajoute une quantité
        // équivalent de ++$cart['elements'][$bookId]['quantity];

        $cart['total'] = $cart['total'] + $book->getPrice(); //recalcule du total : total_panier = total_panier + prix_du_livre (récupéré avec le getPrice);

        // 4. On sauvegarde le nouveau panier
        $sessionInterface->set('cart', $cart); 

        // 5. On redirige l'utilisateur vers la page index du panier
        return $this->redirectToRoute('panier_index'); 

    }

    //supprimer 1 livre (quantité) dans le panier 
    /**
     * @Route("/panier/enlever/{id}", name="panier_delete")
     */
    public function delete(Book $book, SessionInterface $sessionInterface): Response
    {
        // 1. On récupère le panier
        $cart = $sessionInterface->get('cart');
        if ($cart === null){
            $cart= [
                'total' =>0.0,
                'infoProduits' => []
            ];
        }

        // 2. Si le livre n'est pas dans le panier, on ne fait rien 
        $bookId = $book->getId();
        if (!isset($cart['elements'][$bookId])){
            return $this->redirectToRoute('panier_index');
        }

        // 3. Il existe, alors on met à jour les quantités
        $cart['total'] = $cart['total'] - $book->getPrice();
        $cart['elements'][$bookId]['quantity'] = $cart['elements'][$bookId]['quantity'] - 1;

        // 4. Si la quantité est de 0, on l'enlève complètement du panier
        if ($cart['elements'][$bookId]['quantity'] <= 0){
            unset($cart['elements'][$bookId]);
        }

        // 5. On sauvegarde le panier
        $sessionInterface->set('cart', $cart); 

        // 6. On redirige l'utilisateur vers la page index du panier
        return $this->redirectToRoute('panier_index');
 
    }

   
    //vider un panier 
    //valider un panier
}
