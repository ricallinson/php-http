<?php
use php_require\php_http\Request;

/*
    First "require()" the file to test.
*/

$module = new stdClass();
require(__DIR__ . "/../request.php");
$request = $module->exports;

/*
    Now we test it.
*/

describe("php-http/request", function () {

    describe("request.getServerVar()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            assert($request->getServerVar("null") === null);
        });

        it("should return [./node_modules/.bin/tester]", function () {
            $request = new Request();
            assert($request->getServerVar("SCRIPT_FILENAME") === "./node_modules/.bin/tester");
        });
    });

    describe("request.cookie()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            assert($request->cookie("null") === null);
        });

        it("should return [bar]", function () {
            $_COOKIE["foo"] = "bar";
            $request = new Request();
            assert($request->cookie("foo") === "bar");
        });

        it("should return [bar] form uppercase FOO", function () {
            $_COOKIE["foo"] = "bar";
            $request = new Request();
            assert($request->cookie("FOO") === "bar");
        });
    });

    describe("request.param()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            assert($request->param("null") === null);
        });

        it("should return [request-bar]", function () {
            $_REQUEST["foo"] = "request-bar";
            $request = new Request();
            assert($request->param("foo") === "request-bar");
        });

        it("should return [request-bar] form uppercase FOO", function () {
            $_REQUEST["foo"] = "request-bar";
            $request = new Request();
            assert($request->param("FOO") === "request-bar");
        });

        it("should return [get-bar]", function () {
            $_SERVER["REQUEST_METHOD"] = "GET";
            $_GET["foo"] = "get-bar";
            $request = new Request();
            assert($request->param("foo") === "get-bar");
        });

        it("should return [post-bar]", function () {
            $_SERVER["REQUEST_METHOD"] = "POST";
            $_POST["foo"] = "post-bar";
            $request = new Request();
            assert($request->param("foo") === "post-bar");
        });
    });

    describe("request.get()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            assert($request->get("null") === null);
        });

        it("should return [bar]", function () {
            $request = new Request();
            $request->headers["foo"] = "bar";
            assert($request->get("foo") === "bar");
        });

        it("should return [bar] form uppercase FOO", function () {
            $request = new Request();
            $request->headers["foo"] = "bar";
            assert($request->get("FOO") === "bar");
        });

        it("should return [baz]", function () {
            function getallheaders() {
                return array("bar" => "baz");
            }
            $request = new Request();
            assert($request->get("bar") === "baz");
        });
    });

    describe("request.accepts()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            $result = $request->accepts(array("text"));
            assert($result === null);
        });

        it("should return [text]", function () {
            $request = new Request();
            $request->accepted = array("text", "html");
            $result = $request->accepts(array("text", "html"));
            assert($result === "text");
        });
    });

    describe("request.is()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            $request->is("type");
            assert(true);
        });
    });

    describe("request.acceptsCharset()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            $request->acceptsCharset("charset");
            assert(true);
        });
    });

    describe("request.acceptsLanguage()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            $request->acceptsLanguage("lang");
            assert(true);
        });
    });

    describe("request.cfg()", function ()  {

        it("should return [null]", function () {
            $request = new Request();
            assert($request->cfg("foo") === null);
        });

        it("should return [bar]", function () {
            $request = new Request();
            $request->cfg("foo", "bar");
            assert($request->cfg("foo") === "bar");
        });

        it("should return [bar] form uppercase FOO", function () {
            $request = new Request();
            $request->cfg("foo", "bar");
            assert($request->cfg("FOO") === "bar");
        });
    });
});
