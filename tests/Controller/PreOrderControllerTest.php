<?php
namespace App\Tests\PreOrderControllerTest;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\PreOrder;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PreOrderControllerTest extends WebTestCase
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
            'first_name' => 'test3',
            'last_name' => 'test',
            'email' => 'test@test.com',
            'phone_number' => '5554446677',
            'basket_id' => $basket->getId(),
        ]);

        $this->client->request('POST', "/api/pre-order", [], [], ['CONTENT_TYPE' => 'application/json'], $postData);
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('preOrder', $data["data"]);

        $order = $this->em->getRepository(PreOrder::class)->find($data["data"]["preOrder"]["id"]);
        $this->em->remove($order);
        $this->em->remove($order->getBasket());
        $this->em->flush();
    }

    public function testUpdate()
    {
        $order = $this->order();

        $id = $order->getId();

        $putData = json_encode([
            'first_name' => 'test2',
            'last_name' => 'test2',
            'email' => 'test@test.com',
            'phone_number' => '5554446677',
            'status' => PreOrder::STATUS_APPROVE,
            'basket_id' => $order->getBasket()->getId(),
        ]);

        $this->client->request('PUT', "/api/pre-order/$id", [], [], ['CONTENT_TYPE' => 'application/json'], $putData);

        $data = json_decode($this->client->getResponse()->getContent(), true);
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('preOrder', $data["data"]);
        $this->assertEquals($data["data"]["preOrder"]["firstName"], "test2");

        $this->em->remove($order);
        $this->em->remove($order->getBasket());
        $this->em->flush();
    }

    public function testShow()
    {
        $order = $this->order();

        $id = $order->getId();

        $this->client->request('GET', "/api/pre-order/$id");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $this->em->remove($order);
        $this->em->remove($order->getBasket());
        $this->em->flush();
    }

    public function testDelete()
    {
        $order = $this->order();

        $id = $order->getId();

        $this->client->request('DELETE', "/api/pre-order/$id");
        $data = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('preOrder', $data["data"]);
        $this->assertEquals($data["data"]["preOrder"]["id"], $id);
        
        $this->em->remove($order->getBasket());
        $this->em->flush();
    }

    /**
     * @return PreOrder
     */
    private function order(): PreOrder
    {
        $product = $this->em->getRepository(Product::class)->find(1);
        $basket = new Basket();
        $this->em->persist($basket);

        $order = new Order();
        $order->setBasket($basket);
        $order->setProduct($product);
        $order->setQuantity(1);
        $this->em->persist($order);

        $pre = new PreOrder();
        $pre->setBasket($basket);
        $pre->setFirstName("test");
        $pre->setLastName("test");
        $pre->setEmail("test@test.com");
        $pre->setPhoneNumber("5554446677");
        $pre->setStatus(PreOrder::STATUS_PENDING);
        $this->em->persist($pre);
        $this->em->flush();

        return $pre;
    }

}
