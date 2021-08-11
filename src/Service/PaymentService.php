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
        // http://localhost/Symfony/code/boutique/public/payment/succes
        $protocol = 'http';
        if (isset($_SERVER['HTTPS'])){ //si dans l'URL c'est ecrit HTTPs alors le protocol prend cette valeur
            $protocol = 'https';
        }
        $serverName = $_SERVER['SERVER_NAME'];
        $succesUrl = $protocol . '://' . $serverName . '/payment/succes/{CHECKOUT_SESSION_ID}';
        // $protocol = http / $serverName = localhost/Symfony/code/boutique/public/ 

        // 2. cancel URL 
        // http://localhost/Symfony/code/boutique/public/payment/failure
        $cancelUrl = $protocol . '://' . $serverName . '/payment/failure/{CHECKOUT_SESSION_ID}';


        // 3. Elements (détails du panier)
    }
}