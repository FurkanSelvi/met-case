<?php

namespace App\Command;

use App\Entity\PreOrder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateStatusCommand extends Command
{
    protected static $defaultName = 'custom:update-status';
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(null);
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('1 günden eski ön siparişlerin statusunu değiştir')
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        date_default_timezone_set('Europe/Istanbul');
        $io = new SymfonyStyle($input, $output);
        $date = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -1 day'));
        $orders = $this->em->getRepository(PreOrder::class)->pastOneDay($date);
        foreach ($orders as $order) {
            $order->setStatus(PreOrder::STATUS_AUTO_REJECT);
            $this->em->persist($order);
        }
        $this->em->flush();

        $count = count($orders);
        $io->writeln("Updated status count: $count");
        
        $io->success('Statuses updated.');
    }
}
