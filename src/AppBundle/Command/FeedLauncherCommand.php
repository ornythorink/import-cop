<?php

namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use GuzzleHttp\Client;


class FeedLauncherCommand extends ContainerAwareCommand
{

    protected $locale;
    protected $source;
    protected $feed;
    protected $pathToStore;


    protected function configure()
    {
        $this
            ->setName('import:launch')
            ->setDescription('launch the import')
            ->addArgument(
                'source',
                InputArgument::REQUIRED
            )
            ->addArgument(
                'locale',
                InputArgument::REQUIRED
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->locale = $input->getArgument('locale');
        $this->source = $input->getArgument('source');

        $this->canResetFeedTable();

        $client = new Client();
        $response = $client->get('http://127.0.0.1:8000/api/feeds/next/'.$this->source.'/'.$this->locale);

        $this->feed = json_decode( $response->getBody()->getContents() , true);

        $this->flagAsTreated();

        $env = $this->getContainer()->get('kernel')->getEnvironment();

        $csvFile = $this->feed['siteslug']  .
            '-'. strtolower($this->source) . '-' . $env . ".csv";

        $this->setPathToStore($csvFile);

        $this->copyFeed();

        /* @todo faire plus beau http://php-webdeveloper.com/?p=88 */


        echo exec("php app/console import:csv " . $this->source  . " " . $this->feed['id']  . " " . $this->locale  . " " .  $csvFile);
    }

    public function flagAsTreated()
    {
        $client = new Client();
        $response = $client->put('http://127.0.0.1:8000/api/feeds/flag/' . $this->feed['id']);
        $feeds = json_decode($response->getBody()->getContents());

        $this->canResetFeedTable();
    }

    public function canResetFeedTable()
    {

        $client = new Client();
        $response = $client->get('http://127.0.0.1:8000/api/feeds/toprocess/'.$this->source.'/'.$this->locale);
        $flaggedActiveFeedsToProcess = json_decode($response->getBody()->getContents());

        if(count($flaggedActiveFeedsToProcess) == 0)
        {
            $response = $client->get('http://127.0.0.1:8000/api/feeds/active/'.$this->source.'/'.$this->locale);
            $feeds = json_decode($response->getBody()->getContents(), true);
            foreach($feeds as $feed)
            {
                if($feed !== NULL){
                   $data = array( 'json' => array('flagbatched' => 'N') );
                   $response = $client->put('http://127.0.0.1:8000/api/feeds/unflag/' . $feed['id'], $data);
                   $feeds = json_decode($response->getBody()->getContents());
                }
            }
        }
    }

    protected function copyFeed()
    {
        try
        {
            $request = new \GuzzleHttp\Client();
            $response = $request->get(trim($this->feed['feed']));
            $response = $response->getBody()->getContents();

            // @todo separer en deux exception, ajouter ConnectException pour guzzle (url invalide)
            // @todo tojours le cas

            $fp = fopen($this->getPathToStore(), "wb");
            fwrite($fp, $response);
            fclose($fp);

            return true;
        }
        catch (\Exception $e)
        {
            // @todo  un vrai log et une action
            var_dump($e->getMessage());
            // Log the error or something
            return false;
        }

    }

    /**
     * @return mixed
     */
    public function getPathToStore()
    {
        return $this->pathToStore;
    }

    /**
     * @param mixed $pathToStore
     */
    public function setPathToStore($pathToStore)
    {
        $this->pathToStore = $pathToStore;
    }

}