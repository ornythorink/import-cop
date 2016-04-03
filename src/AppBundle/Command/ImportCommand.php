<?php

namespace AppBundle\Command;


use AppBundle\Utils\Sources;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Cocur\Slugify\Slugify;
use GuzzleHttp\Client;

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

        $this->logger = $this->getContainer()->get('logger');

        $locale = $input->getArgument('locale');
        $this->source = $input->getArgument('source');
        $this->prefix = Sources::getSourceKey($this->source,'prefix');
        $this->filename = $input->getArgument('filename');
        $this->feedId = $input->getArgument('feedId');


        $data = $this->getExtractor($this->source,$this->filename);

        $filter = $this->getFilter();

        $filteredCsvArray =   'AppBundle\Utils\\'. $this->prefix . 'FilteredCsvArray';

        $filteredIt = new $filteredCsvArray($data);
        $filteredIt->setBlackList($filter);
        $iterator = $filteredIt->getIterator();

        $cachePending = array();
        foreach ($iterator as $key => $produit) {

            $slugify = new Slugify();
            $slugifiedCategory = $slugify->slugify($produit[Sources::getSourceKey($this->source,'merchantCategoryName')]);

            if(isset($cachePending[$slugifiedCategory]) == false){
                $cachePending[$slugifiedCategory] = $slugifiedCategory;
                $this->createPending($this->checkIfAlreadyPending($produit));
            }

            $client = new Client();
            $client->post('http://127.0.0.1:8000/api/products/import/'.$this->source.'/'.$this->feedId ,
               array( 'body' => $produit)
            );
        }

        unlink($this->filename);
    }

    protected function checkIfAlreadyPending($produit)
    {
        /* @todo il y a du slugifiy partout, methode à part */
        $slugify = new Slugify();
        $slugifiedCategory = $slugify->slugify($produit[Sources::getSourceKey($this->source,'merchantCategoryName')]);

        $client = new Client();
        if(!is_null($slugifiedCategory) || $slugifiedCategory != "")
        {
            $response = $client->get('http://127.0.0.1:8000/api/pendings/' . $slugifiedCategory);

            if($response->getStatusCode() != 204)
            {
                $pending = json_decode($response->getBody()->getContents(), true);
                $check = array('pending' => $pending, 'produit' => $produit);
            } else {
                $pending = array('id' => $slugifiedCategory,
                    'label' => $produit[Sources::getSourceKey($this->source,'merchantCategoryName')],
                    'createdat' => date("Y-m-d H:i:s") );
                $check = array('pending' => $pending , 'produit' => $produit);
            }
        } else {
            $check = null;
        }
        return $check;

    }

    protected function createPending($result){
        /* @todo nettoyer un peu les ifs */
        if(!is_null($result))
        {
            if(!is_null($result['pending'])){
                if(!is_null($result['produit'][Sources::getSourceKey($this->source,'merchantCategoryName')])
                    && $result['produit'][Sources::getSourceKey($this->source,'merchantCategoryName')] != "" )
                {
                    $client = new Client();
                    $response = $client->post('http://127.0.0.1:8000/api/pendings/replace/' . $this->source,
                        array('body' => $result['pending'] ) ) ;
                    /* @todo logger les repronses et ptet broken */
                    //echo $response->getStatusCode();
                }
            }
        }
    }

    protected function getExtractor()
    {
        $fileName = $this->filename;

        /* @todo injecter du container */
        $converter = $this->getContainer()->get('import.csvtoarray');
        /* @todo delimiter and option */

        $data = $converter->convert($fileName, Sources::getSourceKey($this->source , 'separator') );

        return $data;
    }

    protected function getFilter()
    {
        $categories = array();
        $client = new Client();
        $response = $client->get('http://127.0.0.1:8000/api/blacklistcategories');
        $return = json_decode($response->getBody()->getContents() ,true );
        $total = count($return);
        for($i = 0; $i < $total; $i++){
            $categories[] = $return[$i]['pending']['id'];
        }

        return $categories;
    }

    //    protected function setPending( \FilterIterator  $filteredIt)
//    {
//        foreach ($filteredIt as $produit) {
//            //$key = array_keys($produit);
//            echo $produit['MerchantProductCategoryPath'];
//
//            $hop = $this->checkIfAlreadyPending($produit);
//            $this->createPending($this->checkIfAlreadyPending($produit));
//
//             $client = new Client(
//                ['base_uri' => 'http://127.0.0.1:8000']);
//            $client->request('POST',
//                '/api/products/import/'.$this->source.'/'.$this->feedId ,
//                array('content-type' => 'application/json'), array( 'json' => $produit)
//            );
//
//        }
//        exit;
//    }


}