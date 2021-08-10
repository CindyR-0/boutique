<?php

namespace App\Service;

use App\Entity\Book;

// mise en place du service de compteur de prix 
class BookPricerService
{
    public function computePrice (Book $book): void
    {
        // $book->setPrice(strlen($book->getDescritpion()));
        // equivalent plus détaillé : 
         $desc = $book->getDescription(); //recupératon de la descripion du livre
         $newPrice = strlen($desc); //stockage de la longueur de la description sous forme d'un integer
         $book->setPrice($newPrice); //le prix du livre est égale à la longueur de la déscription
    }
}