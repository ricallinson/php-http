<?php
use php_require\php_http\Request;
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
            $response->removeHeader("foo");
            assert($response->get("foo") === null);
        });
    });

    describe("response.cookie()", function () {

        it("should return [php_require\php_http\Response]", function () {
            $response = new Response();
            $result = $response->cookie("foo", "bar");
            assert(get_class($result) === "php_require\php_http\Response");
        });

        it("should return [bar]", function () {
            $response = new Response();
            $response->cookie("foo", "bar");
            assert(true);
        });
    });

    describe("response.clearCookie()", function () {

        it("should return [php_require\php_http\Response]", function () {
            $response = new Response();
            $result = $response->clearCookie("foo");
            assert(get_class($result) === "php_require\php_http\Response");
        });

        it("should return [bar]", function () {
            $response = new Response();
            $response->clearCookie("foo");
            assert(true);
        });
    });

    describe("response.redirect()", function () {

        it("should return [Moved Temporarily. Redirecting to /]", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->redirect("/");
            $result = ob_get_clean();
            assert($response->statusCode === 302);
            assert($response->get("Content-Length") === 35);
            assert($result === "Moved Temporarily. Redirecting to /");
        });
    });

    describe("response.location()", function () {

        it("should return [php_require\php_http\Response]", function () {
            $response = new Response();
            $response->request = new Request();
            $result = $response->location("/");
            assert(get_class($result) === "php_require\php_http\Response");
        });

        it("should return [/]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->location("/");
            assert($response->get("Location") === "/");
        });

        it("should return [http://127.0.0.1/]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->location("http://127.0.0.1/");
            assert($response->get("Location") === "http://127.0.0.1/");
        });

        it("should return [/foo]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->location("./foo");
            assert($response->get("Location") === "/foo");
        });
    });
});
