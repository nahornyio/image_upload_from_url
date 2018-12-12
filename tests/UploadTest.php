<?php

use PHPUnit\Framework\TestCase;
use ImageUploadFromUrl\Upload;

class UploadTest extends TestCase
{

    /**
     * @dataProvider provideImage
     * @throws Exception
     */
    public function testSaveOK($check_field, $remote_url, $folder, $filename, $rewrite)
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped('Extension curl is not loaded.');
        }

        $file = new Upload($remote_url, $folder, $filename, $rewrite);
        $this->assertArrayHasKey( $check_field , $file->save());
    }

    /**
     * @param $a
     * @param $b
     * @param $c
     * @param $d
     * @param $g
     * @dataProvider provideImage
     * @throws Exception
     */
    public function testGetFileInfoOK($check_field, $remote_url, $folder, $filename, $rewrite)
    {

        if (!extension_loaded('curl')) {
            $this->markTestSkipped('Extension curl is not loaded.');
        }

        $file = new Upload($remote_url, $folder, $filename, $rewrite);
        $savedFile = $file->save();
        $data = $file->getFileInfo();

        $checkData = [
            'basename' => basename($savedFile['dirname'].DIRECTORY_SEPARATOR.$savedFile['basename']),
            'dirname' => dirname($savedFile['dirname'].DIRECTORY_SEPARATOR.$savedFile['basename']),
            'realpath' => realpath($savedFile['dirname'].DIRECTORY_SEPARATOR.$savedFile['basename']),
            'pathinfo' => pathinfo($savedFile['dirname'].DIRECTORY_SEPARATOR.$savedFile['basename']),
            'filesize' => filesize($savedFile['dirname'].DIRECTORY_SEPARATOR.$savedFile['basename']),
        ];
        $this->assertArraySubset($checkData, $data);

    }

    /**
     * @param $remote_url
     * @param $folder
     * @param $filename
     * @param $rewrite
     * @dataProvider provideImageBad
     * @expectedException ExceptionOne
     * @throws Exception
     */
    public function testSaveInput($remote_url, $folder, $filename, $rewrite){
        $this->expectException(RuntimeException::class);
        $file = new Upload($remote_url, $folder, $filename, $rewrite);
        $file->save();
    }

    /**
     * @return array
     */
    public function provideImage(){
        return array (
          array ('basename', 'http://habrastorage.org/files/d09/226/c53/d09226c536e54f66a4c0ebcf17f158f0.png', "upload", 'testPng',  true ),
          array ('basename', 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Rotating_earth_%28large%29.gif', "upload", 'testGif',  true ),
          array ('basename', 'https://vignette.wikia.nocookie.net/gravityfalls/images/d/d4/Cutekirby.jpg/revision/latest?cb=20180610210340', "upload", 'testJpg',  true ),
        );
    }

    /**
     * @return array
     */
    public function provideImageBad(){
        return array (
            array ('https://upload.wikimedia.org', "upload", 'testPng',  true ),
            array ('https://upload.wikimedia.org', "upload1", 'testPng',  false ),
            array ('https://upload.wikimedia.org/wikipedia/commons/2/2c/Rotating_earth_%28large%29.gif', "upload", 'testGif',  false ),
        );
    }
}
