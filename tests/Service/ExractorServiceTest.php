<?php

namespace App\Tests\Helper;

use App\Helper\DirectoryParser;
use App\Service\Extractor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExractorServiceTest extends KernelTestCase
{

    public function testParse()
    {
        self::bootKernel();

        $cacheFile = self::$container->getParameter('wiki_cache_structure_file');
        unlink($cacheFile);
        $this->assertEquals(false, file_exists($cacheFile));
        $extract = self::$container->get('App\Service\Extractor');
        $extract->extract();
        $this->assertEquals(true, file_exists($cacheFile));
    }
}