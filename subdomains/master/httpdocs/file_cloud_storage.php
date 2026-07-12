<?php
$main_root = dirname(dirname(dirname(dirname(__FILE__))));
$subdomain_root = dirname(__FILE__);
require_once $main_root . '/private/_common/classes/load_classes.php';
$app = new App($subdomain_root);

$uploader = new StoreCloudR2();

if (!empty($_FILES['file'])) {

    try {

        $upload = $uploader->uploadFile($_FILES['file']);
        echo $upload['etag'];

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file">
    <button type="submit">Carica</button>
</form>

<!--
object(Aws\Result)#586 (2) { 
    ["data":"Aws\Result":private]=> array(5) { 
        ["ETag"]=> string(34) ""a3fd804fda1785ff50d629fcefde638e"" 
        ["ChecksumCRC32"]=> string(8) "+80NeA==" 
        ["VersionId"]=> string(32) "7e6376b3a8a0b43dea3feb4ce5a1e02a" 
        ["@metadata"]=> array(4) { 
            ["statusCode"]=> int(200) 
            ["effectiveUri"]=> string(140) "https://archymedia.f6ebc1813ae3ebc6ec0a520bcd03c73d.r2.cloudflarestorage.com/uploads/2ab500f84ff6ceb2d3bc550e798d7969_dichiarazione_iris.pdf" 
            ["headers"]=> array(9) { 
                ["date"]=> string(29) "Mon, 23 Feb 2026 07:00:07 GMT" 
                ["content-type"]=> string(24) "text/plain;charset=UTF-8" 
                ["content-length"]=> string(1) "0" 
                ["connection"]=> string(10) "keep-alive" 
                ["etag"]=> string(34) ""a3fd804fda1785ff50d629fcefde638e"" 
                ["x-amz-checksum-crc32"]=> string(8) "+80NeA==" 
                ["x-amz-version-id"]=> string(32) "7e6376b3a8a0b43dea3feb4ce5a1e02a" 
                ["server"]=> string(10) "cloudflare" 
                ["cf-ray"]=> string(20) "9d24d8294d98bb9f-FCO" 
            } 
            ["transferStats"]=> array(1) { 
                ["http"]=> array(1) { 
                    [0]=> array(0) { } 
                } 
            } 
        }
        ["ObjectURL"]=> string(140) "https://archymedia.f6ebc1813ae3ebc6ec0a520bcd03c73d.r2.cloudflarestorage.com/uploads/2ab500f84ff6ceb2d3bc550e798d7969_dichiarazione_iris.pdf" 
    } 
    ["monitoringEvents":"Aws\Result":private]=> array(0) { } 
}
-->