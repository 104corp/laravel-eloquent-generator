<?php

namespace Tests;

use Corp104\Eloquent\Generator\CodeWriter;

/**
 * @covers \Corp104\Eloquent\Generator\CodeWriter
 */
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
    public function shouldWriteCodeWhenCallGenerateWithArray()
    {
        $url = $this->root->url() . '/whatever';
        $filePath = 'whatever.php';

        $exceptedPath = $url . '/' . $filePath;
        $exceptedCode = 'whatever-code';

        $this->target->generate([
            $filePath => $exceptedCode,
        ], $url);

        $this->assertTrue(is_file($exceptedPath));
        $this->assertSame($exceptedCode, file_get_contents($exceptedPath));
    }

    /**
     * @test
     */
    public function shouldWriteCodeWhenCallGenerateWithCallable()
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

    /**
     * @test
     */
    public function shouldOverwriteExistCodeWhenSetOverwriteIsTrue()
    {
        $exceptedOld = 'old-code';
        $exceptedNew = 'new-code';

        $url = $this->root->url();
        $relativePath = '/whatever.php';
        $realPath = $url . $relativePath;

        $this->target->setOverwrite(true);

        // Act & Assert
        $this->target->generate([
            $relativePath => $exceptedOld,
        ], $url);

        $this->assertSame($exceptedOld, file_get_contents($realPath));
        $this->assertNotSame($exceptedNew, file_get_contents($realPath));

        // Old code will be overwrite by new code
        $this->target->generate([
            $relativePath => $exceptedNew,
        ], $url);

        $this->assertNotSame($exceptedOld, file_get_contents($realPath));
        $this->assertSame($exceptedNew, file_get_contents($realPath));
    }

    /**
     * @test
     */
    public function shouldOverwriteExistCodeWhenSetOverwriteIsDefaultFalse()
    {
        $exceptedOld = 'old-code';
        $exceptedNew = 'new-code';

        $url = $this->root->url();
        $relativePath = '/whatever.php';
        $realPath = $url . $relativePath;

        // Act & Assert
        $this->target->generate([
            $relativePath => $exceptedOld,
        ], $url);

        $this->assertSame($exceptedOld, file_get_contents($realPath));
        $this->assertNotSame($exceptedNew, file_get_contents($realPath));

        // Cannot overwrite. The result is the same that code before generate.
        $this->target->generate([
            $relativePath => $exceptedNew,
        ], $url);

        $this->assertSame($exceptedOld, file_get_contents($realPath));
        $this->assertNotSame($exceptedNew, file_get_contents($realPath));
    }
}
