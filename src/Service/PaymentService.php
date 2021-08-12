<?php

namespace App\Service;

use \Stripe\StripeClient;

class PaymentService
{
    private $stripe; 
    private $cartService;

    public function __construct(CartService $cartService) //récuperation du service qui gère le panier
    {
        $this->cartService = $cartService; 
        $this->stripe = new StripeClient('sk_test_51JNEMqCuMtM25zVAnQ66a5CZGpbYTF4Mixvope9tawjYT2Bm5ZBPispDQtgDEb73Hxp2MgJmLJtNifdmIfVKmIag00nTULBXrm');// en paramètre : la clé secrète obtenue sur Stripe 
    }

    // function create une session de paiement stripe
    public function create(): string
    {
        // 1. succes URL 
        // http://localhost/Symfony/code/boutique/public/payment/success
        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']){ //si dans l'URL c'est ecrit HTTPs alors le protocol prend cette valeur
            $protocol = 'https';
        }
        $serverName = $_SERVER['SERVER_NAME'];
        $successUrl = $protocol . '://' . $serverName . '/Symfony/code/boutique/public/payment/success/{CHECKOUT_SESSION_ID}';
        // $protocol = http / $serverName = localhost/Symfony/code/boutique/public/ 

        // 2. cancel URL 
        // http://localhost/Symfony/code/boutique/public/payment/failure
        $cancelUrl = $protocol . '://' . $serverName . '/Symfony/code/boutique/public/payment/failure/{CHECKOUT_SESSION_ID}';


        // 3. Elements (détails du panier)
        /**
         * 1 item : (array associatif)
         * amout : le prix de l'article (float)
         * quantity : la quantité de l'article (integer)
         * currency (type de monnaie) : 'eur'(string)
         * name : le nom de l'article (string)
         */

        $items = []; // un array de array associatif
        $panier = $this->cartService->get(); // récupère le panier
        foreach ($panier['elements'] as $element) // boucle qui parcours les éléments du panier 
        {
            // $element (structure): 
            /*
            [
                'book' => $book, (représente un objet)
                'quantity' => 2
            ]*/
            $item = [
                'amount' => $element['book']->getPrice() * 100, //valeur en centime, pour avoir un prix en euro il faut multiplier par 100 ex : en bdd 12€ => stripe 12 centimes * 100 = 1200 centimes d'euros
                'quantity' => $element['quantity'],
                'currency' => 'eur',
                'name' => $element['book']->getTitre()
            ];

            //array_push($items, $item); //ajoute au tableau items un item
            $items[] = $item; //equivalet du array_push
        }

        $sessionId = $this->stripe->checkout->sessions->create([
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => $items
        ]);
        return $sessionId->id;
    }
}