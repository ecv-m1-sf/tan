<?php

namespace App\Command;

use App\Entity\Route;
use App\Entity\Stop;
use App\Entity\Trip;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ImportGtfsCommand.
 */
class ImportGtfsCommand extends Command
{
    protected static $defaultName = 'app:import-gtfs';

    /** @var EntityManagerInterface */
    private $em;

    /**
     * AppImportGtfsCommand constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null); // <=== Astuce
    }

    protected function configure()
    {
        $this
            ->setDescription('Import données TAN')
            ->addArgument('name', InputArgument::REQUIRED, 'Nom de l\'entité à importer.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = __DIR__.'/../../datas/'.strtolower($input->getArgument('name')).'s.txt';
        $entityName = 'App\Entity\\'.self::entityNameTransfo($input->getArgument('name'));
        $io->note('Import du fichier : '.$fileName);
        if ('stop_time' !== $input->getArgument('name')) {
            $fp = file($fileName);
            $progressBar = new ProgressBar($output, \count($fp));
        } else {
            $progressBar = new ProgressBar($output);
        }
        $progressBar->setFormat('debug');
        $progressBar->start();
        $i = 0;
        $keys = [];
        if (false !== ($handle = fopen($fileName, 'r'))) {
            while (false !== ($data = fgetcsv($handle, 1000, ','))) {
                ++$i;
                if (1 === $i) {
                    $keys = $data;
                    continue;
                }
                $data = array_combine($keys, $data);
                $data = $this->loadEntityLink($entityName, $data);
                $entity = $entityName::createFromCsv($data);
                $this->em->persist($entity);
                if (0 === $i % 1000) {
                    $this->em->flush();
                    $this->em->clear();
                }
                $progressBar->advance();
            }
            $this->em->flush();
            $this->em->clear();
            fclose($handle);
        }
        $progressBar->finish();
        $io->newLine(2);
        $io->success('Fin de l\'import');

        return 1;
    }

    /**
     * Chargement des entités liés.
     *
     * @param string $entityName
     * @param array  $datas
     *
     * @return array
     */
    private function loadEntityLink(string $entityName, array $datas): array
    {
        if ('App\Entity\Stop' === $entityName) {
            $datas['parent'] = null;
            if (null !== $datas['parent_station']) {
                $datas['parent'] = $this->em->find(Stop::class, $datas['parent_station']);
            }
        }
        if ('App\Entity\Trip' === $entityName) {
            $datas['route'] = $this->em->find(Route::class, $datas['route_id']);
        }
        if ('App\Entity\StopTime' === $entityName) {
            $datas['trip'] = $this->em->find(Trip::class, $datas['trip_id']);
            $datas['stop'] = $this->em->find(Stop::class, $datas['stop_id']);
        }

        return $datas;
    }

    private static function entityNameTransfo($input, $separator = '_')
    {
        return ucfirst(str_replace($separator, '', ucwords($input, $separator)));
    }
}
