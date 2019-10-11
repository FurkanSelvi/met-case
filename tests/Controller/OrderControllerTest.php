<?php
namespace App\Tests\OrderControllerTest;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    /**
     * @var Client $client
     */
    private $client;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected function setUp()
    {
        $this->client = $this->createClient();
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testCreate()
    {
        $basket = new Basket();
        $this->em->persist($basket);
        $this->em->flush();

        $postData = json_encode([
            'quantity' => 2,
            'basket_id' => $basket->getId(),
            'product_id' => 1,
        ]);

        $this->client->request('POST', "/api/order", [], [], ['CONTENT_TYPE' => 'application/json'], $postData);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('order', $data["data"]);
        
        $order = $this->em->getRepository(Order::class)->find($data["data"]["order"]["id"]);
        $this->em->remove($order);
        $this->em->remove($order->getBasket()); 
        $this->em->flush();
    }

    public function testUpdate()
    {
        $order = $this->order();

        $id = $order->getId();

        $putData = json_encode([
            'quantity' => 2,
            'basket_id' => $order->getBasket()->getId(),
            'product_id' => 1,
        ]);

        $this->client->request('PUT', "/api/order/$id", [], [], ['CONTENT_TYPE' => 'application/json'], $putData);

        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('order', $data["data"]);
        $this->assertEquals($data["data"]["order"]["quantity"], 2);

        $this->em->remove($order);
        $this->em->remove($order->getBasket()); 
        $this->em->flush();
    }

    public function testShow()
    {
        $order = $this->order();

        $id = $order->getId();

        $this->client->request('GET', "/api/order/$id");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->em->remove($order);
        $this->em->remove($order->getBasket()); 
        $this->em->flush();
    }

    public function testDelete()
    {
        $order = $this->order();

        $id = $order->getId();

        $this->client->request('DELETE', "/api/order/$id");
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('order', $data["data"]);
        $this->assertEquals($data["data"]["order"]["id"], $id);

        $this->em->remove($order->getBasket()); 
        $this->em->flush();
    }

    /**
     * @return Order
     */
    private function order(): Order
    {
        $product = $this->em->getRepository(Product::class)->find(1);
        $basket = new Basket();
        $this->em->persist($basket);

        $order = new Order();
        $order->setBasket($basket);
        $order->setProduct($product);
        $order->setQuantity(1);
        $this->em->persist($order);
        $this->em->flush();

        return $order;
    }

}
