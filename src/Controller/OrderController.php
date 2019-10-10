<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class OrderController extends ApiController
{
    protected $name = 'order';
    /**
     * @Route("/order", methods={"POST"}, name="create_order")
     */
    public function create(Request $request)
    {
        return $this->createOrUpdate($request);
    }

    /**
     * @Route("/order/{id}", methods={"PUT"}, name="update_order", requirements={"id"="\d+"})
     */
    public function update($id, Request $request)
    {
        return $this->createOrUpdate($request, $id);
    }

    /**
     * @Route("/order/{id}", methods={"GET"}, name="show_order", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        if (!$order) {
            return $this->response(null, 'Aradığınız değer bulunamadı', 404, 0);
        }
        return $this->response($this->getResult($order));
    }

    /**
     * @Route("/order/{id}", methods={"DELETE"}, name="delete_order", requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        $order = $this->em->getRepository(Order::class)->find($id);
        if (!$order) {
            return $this->response(null, 'Aradığınız değer bulunamadı', 404, 0);
        }
        $res = $this->getResult($order);
        $this->em->remove($order);
        $this->em->flush();

        return $this->response($res);
    }

    protected function createOrUpdate($request, $id = false)
    {
        $order = new Order();
        if ($id) {
            $order = $this->em->getRepository(Order::class)->find($id);
            if (!$order) {
                return $this->response(null, 'Aradığınız değer bulunamadı', 404, 0);
            }
        }

        $req = json_decode($request->getContent(), true);

        $basketId = isset($req['basket_id']) ? $req['basket_id'] : 0;
        $productId = isset($req['product_id']) ? $req['product_id'] : 0;
        $quantity = isset($req['quantity']) ? $req['quantity'] : null;

        $basket = $this->em->getRepository(Basket::class)->find($basketId);
        $product = $this->em->getRepository(Product::class)->find($productId);

        if (!($basket && $product)) {
            $error = [];
            if (!$basket) {
                $error['basket_id'] = "Sepet bulunamadı";
            }

            if (!$product) {
                $error['product_id'] = "Ürün bulunamadı";
            }

            return $this->response($error, 'Girilen değerleri kontrol ediniz', 422, 0);
        }

        $order->setBasket($basket);
        $order->setProduct($product);
        $order->setQuantity($quantity);

        $validate = $this->validate($order);
        if ($validate) {
            return $this->response($validate, 'Girilen değerleri kontrol ediniz', 422, 0);
        }

        $this->em->persist($order);
        $this->em->flush();

        return $this->response($this->getResult($order));
    }

}
