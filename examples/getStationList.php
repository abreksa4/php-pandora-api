<?php

require_once(__DIR__ . '/../vendor/autoload.php');

$p = \php_pandora_api\PandoraHelper::create();

if (!$p->login($argv[1], $argv[2])) {
    die(sprintf("Error: %s\nReq: %s\n Resp: %s", $p->getPandora()->last_error, $p->getPandora()->last_request_data, $p->getPandora()->last_response_data));
}

if (!$response = $p->getStations()) {
    die(sprintf("Error: %s\nReq: %s\n Resp: %s", $p->getPandora()->last_error, $p->getPandora()->last_request_data, $p->getPandora()->last_response_data));
}

$songs = $p->getSongs($response[0]['stationToken']);

echo '<pre>';
echo json_encode($songs, JSON_PRETTY_PRINT);
echo '</pre>';
