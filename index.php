<?php
namespace php_require\php_http;

$response = $require("./lib/response");
$request = $require("./lib/request");

$response->request = $request;

$module->exports = $response;