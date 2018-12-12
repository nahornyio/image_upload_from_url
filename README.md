# Image-Upload-Url

Simple Easy To use PHP Uploader File From Url
## install Via composer :
```composer require aleksnagornyi/image_upload_from_url```

### Simple File upload:
```php
<?php
try{
    $file = new \ImageUploadFromUrl\Upload('http://habrastorage.org/files/d09/226/c53/d09226c536e54f66a4c0ebcf17f158f0.png', "upload", 'kartinka', true);
    var_dump( $file->save() );
    var_dump( $file->getFileInfo() );
}catch (Exception $e){
    print $e->getMessage();
}

// this will output an array which contains uploaded files data
// array(4) {
  // ["dirname"]=>
  // string(6) "upload"
  // ["basename"]=>
  // string(12) "kartinka.png"
  // ["extension"]=>
  // string(3) "png"
  // ["filename"]=>
  // string(8) "kartinka"
// }
// array(5) {
  // ["basename"]=>
  // string(12) "kartinka.png"
  // ["dirname"]=>
  // string(6) "upload"
  // ["realpath"]=>
  // string(54) "G:\OSPanel\domains\upload_file_url\upload\kartinka.png"
  // ["pathinfo"]=>
  // array(4) {
    // ["dirname"]=>
    // string(6) "upload"
    // ["basename"]=>
    // string(12) "kartinka.png"
    // ["extension"]=>
    // string(3) "png"
    // ["filename"]=>
    // string(8) "kartinka"
  // }
  // ["filesize"]=>
  // int(89732)
// }

```