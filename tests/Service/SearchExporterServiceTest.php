<?php

namespace App\Tests\Helper;

use App\Enum\SearchIndex;
use App\Helper\DirectoryParser;
use App\Service\Extractor;
use MeiliSearch\Exceptions\ApiException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SearchExporterServiceTest extends KernelTestCase
{

    public function testParse()
    {
        self::bootKernel();

        $meili = $extract = self::$container->get('MeiliSearch\Client');
        $index = $meili->getOrCreateIndex(SearchIndex::PAGE);
        $index->delete();

        try {
            $meili->getIndex(SearchIndex::PAGE);
        } catch (\Throwable $e) {
            $this->assertTrue($e instanceof ApiException);
        }

        $exporter = self::$container->get('App\Service\SearchExporter');
        $exporter->export();

        sleep(1);
        $result = $exporter->search("консоль");

        $this->assertEquals(1, count($result));
        $this->assertEquals("5fa9d7d23663dcde955240218f66aeee", $result[0]['id']);
    }
}