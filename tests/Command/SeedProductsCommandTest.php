<?php
namespace App\Tests\SeedProductsCommandTest;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class SeedProductsCommandTest extends KernelTestCase
{

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('custom:seed-products');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
            ]
        );

        $output = $commandTester->getDisplay();
        $this->assertContains('Product count: 11', $output);
    }

}
