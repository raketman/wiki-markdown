<?php

namespace App\Tests\Helper;

use App\Helper\DirectoryParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DirectoryParserTest extends KernelTestCase
{
    public function testParse()
    {
        self::bootKernel();
        $parser = new DirectoryParser(self::$container->getParameter('wiki_source_dir'));

        $data = $parser->parse();
        $this->assertEquals("", $data->getPath());
        $this->assertEquals("Корневая папка", $data->getName());

        $this->assertEquals(3, count($data->getChilds()));

        $first = $data->getChilds()[0];
        $this->assertEquals("/api.markdown", $first->getPath());
        $this->assertEquals("markdown", $first->getOptions()->getExtension());

    }
}