<?php

namespace Tests;

use Corp104\Eloquent\Generator\CodeWriter;

class CodeWriterTest extends TestCase
{
    /**
     * @var CodeWriter
     */
    private $target;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new CodeWriter();
    }

    protected function tearDown()
    {
        $this->target = null;

        parent::tearDown();
    }

    /**
     * @test
     */
    public function shouldReturnKeyIsOnlyTableNameWithSingleDatabase()
    {
        $url = $this->root->url() . '/whatever';
        $filePath = 'whatever.php';

        $exceptedPath = $url . '/' . $filePath;
        $exceptedCode = 'whatever-code';

        $this->target->generate(function () use ($filePath, $exceptedCode) {
            return [
                $filePath => $exceptedCode,
            ];
        }, $url);

        $this->assertTrue(is_file($exceptedPath));
        $this->assertSame($exceptedCode, file_get_contents($exceptedPath));
    }
}
