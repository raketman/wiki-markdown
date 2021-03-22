<?php

namespace App\Command;

use App\Service\Extractor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ActualizeWikiCommand extends Command
{
    /** @var Extractor  */
    private $extractor;

    public function __construct(Extractor $extractor)
    {
        $this->extractor = $extractor;
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
        return 0;
    }
}
