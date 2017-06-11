<?php

require_once '../src/php_pandora_api/Pandora.php';

use php_pandora_api\Pandora;

$p = new Pandora('android');

if (!$p->login($argv[1], $argv[2])) {
    die(sprintf("Error: %s\nReq: %s\n Resp: %s", $p->last_error, $p->last_request_data, $p->last_response_data));
}

if (!$response = $p->makeRequest(Pandora::user_getStationList)) {
    die(sprintf("Error: %s\nReq: %s\n Resp: %s", $p->last_error, $p->last_request_data, $p->last_response_data));
}

echo '<pre>';
print_r($response);
echo '</pre>';
