<?php
namespace App\Tests\UpdateStatusCommandTest;

use App\Entity\Basket;
use App\Entity\Order;
use App\Entity\PreOrder;
use App\Entity\Product;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateStatusCommandTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testExecute()
    {
        date_default_timezone_set('Europe/Istanbul');
        $date = new DateTime();

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
        $pre->setCreatedAt($date->modify('-2 day'));
        $this->em->persist($pre);

        $this->em->flush();

        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('custom:update-status');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $newPre = $this->em->getRepository(PreOrder::class)->find($pre->getId());
        $this->assertEquals($newPre->getStatus(), PreOrder::STATUS_AUTO_REJECT);

        $basket = $this->em->merge($basket);
        $order = $this->em->merge($order);
        $pre = $this->em->merge($pre);

        $this->em->remove($pre);
        $this->em->remove($order);
        $this->em->remove($basket);
        $this->em->flush();
    }

}
