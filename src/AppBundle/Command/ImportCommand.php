<?php

namespace AppBundle\Command;


use AppBundle\Utils\Sources;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Cocur\Slugify\Slugify;


class ImportCommand extends ContainerAwareCommand
{

    protected $source;
    protected $filename;
    protected $prefix;

    protected function configure()
    {
        $this
            ->setName('import:csv')
            ->setDescription('Import products from CSV file')
            ->addArgument(
                'source',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'feedId',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'locale',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'filename',
                InputArgument::REQUIRED
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $locale = $input->getArgument('locale');
        $this->source = $input->getArgument('source');
        $this->prefix = Sources::getSourceKey($this->source,'prefix');


        $this->filename = $input->getArgument('filename');
        $this->feedId = $input->getArgument('feedId');

        $iterator = $this->getExtractor($this->source,$this->filename);
        $filter = $this->getFilter();

        $FilteredCsvArray =   '\Cop\ImportBundle\Utils\\'. $this->prefix . 'FilteredCsvArray';

        $filteredIt = new $FilteredCsvArray($iterator, $filter);

        $this->putPending($filteredIt);
    }

    protected function putPending($filteredIt)
    {
        $repoProducts = $this->getContainer()
            ->get('doctrine')
            ->getManager()->getRepository('Cop\DataStoreBundle\Entity\Products');

        foreach ($filteredIt as $produit) {
            $this->createPending($this->checkIfAlreadyPending($produit));
            $import = $this->prefix . 'ImportCsv';
            $repoProducts->$import($produit, $this->feedId);
        }
    }

    protected function checkIfAlreadyPending($produit)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $slugify = new Slugify();
        $slugifiedCategory = $slugify->slugify($produit[ Sources::getSourceKey($this->source,'merchantCategoryName')  ]);

        $pending = $em->getRepository('Cop\DataStoreBundle\Entity\Pending')->findOneBy(
            array(
                'id' => $slugifiedCategory
            )
        );

        return array('pending' => $pending, 'produit' => $produit);
    }

    protected function createPending($result){
        $em = $this->getContainer()->get('doctrine')->getManager();
        $pendingRepo = $em->getRepository('Cop\DataStoreBundle\Entity\Pending');

        if(is_null($result['pending'])){
            if(!is_null($result['produit'][Sources::getSourceKey($this->source,'merchantCategoryName')])
                && $result['produit'][Sources::getSourceKey($this->source,'merchantCategoryName')] != "" )
            {
                $pendingRepo->createOrReplacePending($result,$this->source);
            }
        }

    }

    protected function getExtractor()
    {
        $fileName = $this->filename;

        /* @todo injecter du container */
        $converter = $this->getContainer()->get('import.csvtoarray');
        /* @todo delimiter and option */
        $iterator = $converter->convert($fileName, Sources::getSourceKey($this->source , 'separator') );

        return $iterator;
    }

    protected function getFilter()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $repoBlack = $em->getRepository('\Cop\DataStoreBundle\Entity\BlacklistCategories');
        $categories = $repoBlack->findAll();

        return $categories;
    }

}