<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\PreOrder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class PreOrderController extends ApiController
{

    protected $name = 'preOrder';
    /**
     * @Route("/pre-order", methods={"POST"}, name="create_pre_order")
     */
    public function create(Request $request)
    {
        return $this->createOrUpdate($request);
    }

    /**
     * @Route("/pre-order/{id}", methods={"PUT"}, name="update_pre_order", requirements={"id"="\d+"})
     */
    public function update($id, Request $request)
    {
        return $this->createOrUpdate($request, $id);
    }

    /**
     * @Route("/pre-order/{id}", methods={"GET"}, name="show_pre_order", requirements={"id"="\d+"})
     */
    public function show($id)
    {
        $order = $this->em->getRepository(PreOrder::class)->find($id);
        if (!$order) {
            return $this->response(null, 'Aradığınız değer bulunamadı', 404, 0);
        }
        return $this->response($this->getResult($order));
    }

    /**
     * @Route("/pre-order/{id}", methods={"DELETE"}, name="delete_pre_order", requirements={"id"="\d+"})
     */
    public function delete($id)
    {
        $order = $this->em->getRepository(PreOrder::class)->find($id);
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
        $order = new PreOrder();
        if ($id) {
            $order = $this->em->getRepository(PreOrder::class)->find($id);
            if (!$order) {
                return $this->response(null, 'Aradığınız değer bulunamadı', 404, 0);
            }
        }

        $req = json_decode($request->getContent(), true);

        $basketId = isset($req['basket_id']) ? $req['basket_id'] : 0;
        $firstName = isset($req['first_name']) ? $req['first_name'] : null;
        $lastName = isset($req['last_name']) ? $req['last_name'] : null;
        $email = isset($req['email']) ? $req['email'] : null;
        $phoneNumber = isset($req['phone_number']) ? $req['phone_number'] : null;
        $status = isset($req['status']) ? $req['status'] : PreOrder::STATUS_PENDING;


        $basket = $this->em->getRepository(Basket::class)->find($basketId);
        if (!$basket) {
            return $this->response(['basket_id' => "Sepet bulunamadı"], 'Girilen değerleri kontrol ediniz', 422, 0);
        }

        $order->setBasket($basket);
        $order->setFirstName($firstName);
        $order->setLastName($lastName);
        $order->setEmail($email);
        $order->setPhoneNumber($phoneNumber);
        $order->setStatus($status);

        $validate = $this->validate($order);
        if ($validate) {
            return $this->response($validate, 'Girilen değerleri kontrol ediniz', 422, 0);
        }

        $this->em->persist($order);
        $this->em->flush();

        return $this->response($this->getResult($order));
    }
}
