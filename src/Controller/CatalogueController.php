<?php

namespace App\Controller;


use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/catalogue")
 */
class CatalogueController extends AbstractController
{
    /**
     * @Route("/", name="catalogue_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('catalogue/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

}
