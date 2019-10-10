<?php

namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SeedProductsCommand extends Command
{
    protected static $defaultName = 'custom:seed-products';
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(null);
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Ürünleri ekle')
            // ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $classMetaData = $this->em->getClassMetadata(Product::class);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($classMetaData->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollback();
        }
        $products = [
            "iPhone 11 Pro Max", "iPhone 11 Pro", "iPhone 11",
            "iPhone XS Max", "iPhone XS", "iPhone XR", "iPhone X",
            "iPhone 8 Plus", "iPhone 8", "iPhone 7 Plus", "iPhone 7",
        ];
        foreach ($products as $product) {
            $productM = new Product();
            $productM->setName($product);
            $this->em->persist($productM);
        }
        $this->em->flush();

        $qb = $this->em->createQueryBuilder();
        $qb->select('count(p.id)');
        $qb->from(Product::class,'p');
        $count = $qb->getQuery()->getSingleScalarResult();
        $io->writeln("Product count: $count");

        $io->success('Seed Success.');
    }
}
