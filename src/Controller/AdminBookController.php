<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Service\BookPricerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/book")
 */
class AdminBookController extends AbstractController
{
    /**
     * @Route("/", name="admin_book_index", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('admin_book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_book_new", methods={"GET","POST"})
     */
    public function new(Request $request, BookPricerService $bookPricerService): Response
    { //BookPricerService $bookPricerService indique l'utilisation du service dans la méthode
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookPricerService->computePrice($book); //appel du service du calcule de prix
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('admin_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_book_show", methods={"GET"})
     */
    public function show(Book $book): Response
    {
        return $this->render('admin_book/show.html.twig', [
            'book' => $book,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_book_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Book $book, BookPricerService $bookPricerService): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookPricerService->computePrice($book); //appel du service du calcule de prix
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin_book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_book_delete", methods={"POST"})
     */
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($book);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_book_index', [], Response::HTTP_SEE_OTHER);
    }

}
