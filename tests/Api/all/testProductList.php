<?php
require_once './CurlRequest.php';

function testProductList() {
    $request = new CurlRequest('http://mif-api.local/product/list?limit=1&page=1');
    $json = json_decode($request->execute());
    if ($json) {
        echo $request->echoOk();
    } else {
        echo $request->echoError();
    }
    $request->close();
}
