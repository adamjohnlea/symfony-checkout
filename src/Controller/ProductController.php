<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ProductRepository;

class ProductController extends AbstractController {
    /**
     * @Route("/")
     * @param ProductRepository $repo
     * @return Response
     */
    public function homepage(ProductRepository $repo): Response {
        $bikes = $repo->findBy([]);
        return $this->render('homepage.html.twig', [
            'bikes' => $bikes
        ]);
    }

    /**
     * @Route("/products/{id}")
     * @param $id
     * @param ProductRepository $repo
     * @return Response
     */
    public function details($id, Request $request, ProductRepository $repo, SessionInterface $session): Response {
        $bike = $repo->find($id);

        if ($bike === null) {
            throw $this->createNotFoundException('That product could not be found');
        }

        // Add to basket handlers
        $basket = $session->get('basket', []);

        if ($request->isMethod('POST')) {
            $basket[$bike->getId()] = $bike;
            $session->set('basket', $basket);
        }

        $isInBasket = array_key_exists($bike->getId(), $basket);

        return $this->render('details.html.twig', [
            'bike' => $bike,
            'inBasket' => $isInBasket
        ]);
    }
}