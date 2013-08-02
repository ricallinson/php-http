<?php
use php_require\php_http\Response;

/*
    First "require()" the file to test.
*/

$module = new stdClass();
$require = function () {};
require(__DIR__ . "/../response.php");

/*
    Now we test it.
*/

describe("php-http/response", function () {

    describe("response.status()", function () {

        it("should return [404]", function () {
            $response = new Response();
            $result = $response->status(404);
            assert($response->statusCode === 404);
            assert(get_class($result) === "php_require\php_http\Response");
        });
    });

    describe("response.set() and response.get()", function () {

    	it("should return [php_require\php_http\Response]", function () {
            $response = new Response();
            $result = $response->set("foo", "bar");
            assert(get_class($result) === "php_require\php_http\Response");
        });

    	it("should return [bar]", function () {
            $response = new Response();
            $response->set("foo", "bar");
            assert($response->get("foo") === "bar");
        });

        it("should return [bar] form uppercase FOO", function () {
            $response = new Response();
            $response->set("foo", "bar");
            assert($response->get("FOO") === "bar");
        });

        it("should return [null]", function () {
            $response = new Response();
            assert($response->get("foo") === null);
        });
    });

    describe("response.removeHeader()", function () {

    	it("should return [php_require\php_http\Response]", function () {
            $response = new Response();
            $result = $response->removeHeader("foo");
            assert(get_class($result) === "php_require\php_http\Response");
        });

        it("should return [bar]", function () {
            $response = new Response();
            $response->set("foo", "bar");
            assert($response->get("foo") === "bar");
        });

        it("should return [null]", function () {
            $response = new Response();
            $response->set("foo", "bar");
            $result = $response->removeHeader("foo");
            assert($response->get("foo") === null);
        });
    });
});
