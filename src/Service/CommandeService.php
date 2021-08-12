<?php

namespace App\Service;

use App\Entity\Book;
use DateTimeImmutable;
use App\Entity\Commande;
use App\Service\CartService;
use App\Entity\CommandeDetail;
use Doctrine\ORM\EntityManagerInterface;




class CommandeService 
{
    private $cartService;

    public function __construct( CartService $cartService, EntityManagerInterface $em)
    { 
        $this->cartService = $cartService;
        $this->em = $em;
        
    }

    public function create(string $stripeSessionId)
    {
        $date = new DateTimeImmutable('NOW');
        $cart= $this->cartService->get('cart');
        $commande = new Commande;
        $commande->setCreatedAt($date);
        $commande->setReference($stripeSessionId);
        foreach($cart['elements'] as $element){
            $commandeDetail = new CommandeDetail;
            $commandeDetail->setQuantity($element['quantity']);
            $commande->addCommandeDetail($commandeDetail);
            $book = $this->em->getRepository(Book::class)->find($element['book']->getId());
            $book->addCommandeDetail($commandeDetail);
            $this->em->persist($book);
        }
        $this->em->persist($commande);
        $this->em->flush();
    }

   

}