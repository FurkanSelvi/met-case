<?php
namespace App\Tests\BookControllerTest;

use App\Entity\Basket;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
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
        $this->client->request('POST', "/api/basket");
        $data = json_decode($this->client->getResponse()->getContent(),true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('basket', $data["data"]);

        $basket = $this->em->getRepository(Basket::class)->find($data["data"]["basket"]["id"]);
        $this->em->remove($basket);
        $this->em->flush();
    }

    public function testShow()
    {
        $basket = new Basket();
        $this->em->persist($basket);
        $this->em->flush();
        $id = $basket->getId();

        $this->client->request('GET', "/api/basket/$id");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $basket = $this->em->merge($basket);
        $this->em->remove($basket);
        $this->em->flush();
    }

    public function testDelete()
    {
        $basket = new Basket();
        $this->em->persist($basket);
        $this->em->flush();
        $id = $basket->getId();

        $this->client->request('DELETE', "/api/basket/$id");
        $data = json_decode($this->client->getResponse()->getContent(),true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('basket', $data["data"]);
        $this->assertEquals($data["data"]["basket"]["id"], $id);
    }

}
