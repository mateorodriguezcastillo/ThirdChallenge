<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;

class ShowCommand extends Command
{

    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;

        parent::__construct();
    }

    public function configure()
    {
        $this->setName('show')
             ->setDescription('Show movie details')
             ->addArgument('movieName', InputArgument::REQUIRED);
            
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $link = 'http://www.omdbapi.com/?apikey=7c250955&t=' . $input->getArgument(('movieName'));
        $data = json_decode($this->download($link), true);
        $message = $data['Title'] . ' - ' . $data['Year'];
        $output->writeln("<info>{$message}</info>");

        $table = new Table($output);
        foreach ($data as $key => $value) {
            if (gettype($value) == 'string') {
                $table->addRows([
                    [$key, $value]
                ]);
            }
        }
        $table->render();
    }

    private function download(String $link)
    {
        return $this->client->get($link)->getBody();
    }

}
