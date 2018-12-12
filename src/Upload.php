<?php

namespace ImageUploadFromUrl;

/**
 * Class Upload
 * @package ImageUploadFromUrl
 */
class Upload
{
    /**
     * @var string|string
     */
    private $remote_url, $filename, $folder, $filePath, $imageType, $rewrite;

    /**
     * Upload constructor.
     *
     * @param string $remote_url
     * @param string $folder
     * @param bool $filename
     * @param bool $rewrite
     * @throws \Exception
     */
    public function __construct(string $remote_url, string $folder, $filename = false, $rewrite = false)
    {
        if(!filter_var($remote_url, FILTER_VALIDATE_URL)){
            throw new \Exception('Not a valid URL');
        }
        $this->remote_url = $remote_url;

        if( !is_dir( $folder ) ){
            throw new \Exception('Folder Not Exist');
        }
        if( !is_writable ( $folder ) ){
            throw new \Exception('Directory Is Not Writable');
         }

        $this->folder = $folder;
        if($filename == false){
            $this->filename = uniqid();
        }else{
            $this->filename = $filename;
        }
        $this->rewrite = $rewrite;
    }

    /**
     * save image method
     *
     * @return bool|string
     * @throws \Exception
     */
    public function save()
    {
        $dataImage = $this->getImageFromUrl();
        $this->filePath = $this->folder.DIRECTORY_SEPARATOR.$this->filename.'.'.$this->imageType;

        if($this->rewrite == false && file_exists($this->filePath)) throw new \Exception('File Exists');

        switch ($this->imageType){
            case 'gif':
                imagegif($dataImage, $this->filePath);
                break;
            case 'jpg':
                imagejpeg($dataImage, $this->filePath, 100);
                break;
            case 'png':
                imagepng($dataImage, $this->filePath );
                break;
            default:
                throw new \Exception('Not Suported Mime Type');
        }
        imagedestroy($dataImage);

        return pathinfo($this->filePath);
    }

    /**
     * Full file info
     *
     * @return array
     */
    public function getFileInfo():array
    {
        return [
            'basename' => basename($this->filePath),
            'dirname' => dirname($this->filePath),
            'realpath' => realpath($this->filePath),
            'pathinfo' => pathinfo($this->filePath),
            'filesize' => filesize($this->filePath),
        ];
    }

    /**
     * curl get image from url and return image recource
     *
     * @return resource
     * @throws \Exception
     */
    private function getImageFromUrl()
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('Curl Not Loadet');
        }

        $ch = curl_init($this->remote_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($ch);


        if(curl_getinfo($ch, CURLINFO_RESPONSE_CODE) !== 200){
            throw new \Exception('Image Not Found');
        }

        if(curl_getinfo($ch, CURLINFO_CONTENT_TYPE ) === NULL){
            throw new \Exception('Server Dont Send Type');
        }

        $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);

        curl_close($ch);

        if($this->checkFileExtensions($type) == false) {
            throw new \Exception('Not Suported Mime Type');
        }

        $imageResource = @imagecreatefromstring($result);

        if( !$imageResource ){
            throw new \Exception('Not Suported Mime Type');
        }
        return $imageResource;
    }

    /**
     * check type exist image
     *
     * @param string $type
     * @return bool
     */
    private function checkFileExtensions(string $type):bool
    {
        $ext = ["image/gif" => 'gif', "image/jpeg" => 'jpg', "image/png" => 'png'];
        if (array_key_exists($type, $ext)) {
            $this->imageType = $ext[$type];
            return true;
        }
        return false;
    }

}