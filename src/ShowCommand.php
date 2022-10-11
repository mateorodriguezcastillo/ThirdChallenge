<?php namespace Acme;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

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
             ->addArgument('movieName', InputArgument::REQUIRED)
             ->addOption('fullPlot', null, InputOption::VALUE_NONE, 'Displays full plot of the movie');
            
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $link = 'http://www.omdbapi.com/?apikey=7c250955&t=' . $input->getArgument(('movieName'));
        if ($input->getOption('fullPlot')) {
            $link = $link . '&plot=full';
        }
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
        $table->setColumnMaxWidth(1, 150);
        $table->render();
        return 0;
    }

    private function download(String $link)
    {
        return $this->client->get($link)->getBody();
    }

}
