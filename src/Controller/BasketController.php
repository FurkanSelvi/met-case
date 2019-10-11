<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\PreOrder;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class BasketController extends ApiController
{
    protected $name = 'basket';
    /**
     * @Route("/basket", methods={"POST"}, name="create_basket")
     */
    public function create()
    {
        $basket = new Basket();
        $this->em->persist($basket);
        $this->em->flush();

        return $this->response($this->getResult($basket));
    }

    /**
     * @Route("/basket/{id}", methods={"GET"}, name="show_basket", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $basket = $this->em->getRepository(Basket::class)->find($id);
        if(!$basket) {
            return $this->response(null, 'Aradığınız değer bulunamadı',404, 0);
        } 
        return $this->response($this->getResult($basket));
    }

    /**
     * @Route("/basket/{id}", methods={"DELETE"}, name="delete_basket", requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        $basket = $this->em->getRepository(Basket::class)->find($id);
        if(!$basket) {
            return $this->response(null, 'Aradığınız değer bulunamadı',404, 0);
        } 
        $res = $this->getResult($basket); 
        $this->em->remove($basket);
        $this->em->flush();

        return $this->response($res);
    }

}
