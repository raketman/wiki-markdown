<?php

namespace App\Command;

use App\Service\Extractor;
use App\Service\SearchExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActualizeWikiCommand extends Command
{
    /** @var Extractor  */
    private $extractor;

    /** @var SearchExporter  */
    private $searchExporter;

    public function __construct(Extractor $extractor, SearchExporter $searchExporter)
    {
        $this->extractor = $extractor;
        $this->searchExporter = $searchExporter;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:wiki:actualize')
            ->setDescription('Обновляем справочник вики')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->extractor->extract();

        $this->searchExporter->export();
        return 0;
    }
}
