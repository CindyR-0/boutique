<?php

namespace App\Service;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $sessionInterface;

    public function __construct(SessionInterface $sessionInterface)
    {
        $this->sessionInterface = $sessionInterface; //on attribut au paramètre private la $sessionInterface.
    }

    public function get(): array
    { //methode de la récupération du panier et dans le cas ou il n'existe pas, on en crée un.
        $cart = $this->sessionInterface->get('cart');
        if ($cart === null){
            $cart= [
                'total' =>0.0,
                'elements' => []
            ];
        }
        return $cart;
    }

    //ajout
    public function add(Book $book): void
    {
        // 1. On récupère le panier s'il existe
        $cart = $this->get();
       
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
        $this->sessionInterface->set('cart', $cart); 

    }

    public function delete(Book $book): void
    {
        // 1. On récupère le panier
        $cart = $this->get();

        // 2. Si le livre n'est pas dans le panier, on ne fait rien 
        $bookId = $book->getId();
        if (!isset($cart['elements'][$bookId])){
            return;
        }

        // 3. Il existe, alors on met à jour les quantités
        $cart['total'] = $cart['total'] - $book->getPrice();
        $cart['elements'][$bookId]['quantity'] = $cart['elements'][$bookId]['quantity'] - 1;

        // 4. Si la quantité est de 0, on l'enlève complètement du panier
        if ($cart['elements'][$bookId]['quantity'] <= 0){
            unset($cart['elements'][$bookId]);
        }

        // 5. On sauvegarde le panier
        $this->sessionInterface->set('cart', $cart); 

    }

    public function clear()
    {
        $this->sessionInterface->remove('cart');
    }

    public function removeLine()
    {
       // 1. On récupère le panier
       $cart = $this->get();
    
       // 2. Si le livre n'est pas dans le panier on ne fait rien
       $bookId = $book->getId();
       if (!isset($cart['elements'][$bookId])){
           return;
       }

       // 3. On met à jour le total et on sucre la ligne (sucre = supprimer)
       $cart['total'] = $cart['total'] - $book->getPrice() * $cart['elements'][$bookId]['quantity'];
       unset($cart['elements'][$bookId]);

       // 4. On enregistre le panier
       $this->sessionInterface->set('cart', $cart);  
    }
}