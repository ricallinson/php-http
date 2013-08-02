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
            $response->request->accepted = array("text/plain");
            ob_start();
            $response->redirect("/");
            $result = ob_get_clean();
            assert($response->statusCode === 302);
            assert($response->get("Content-Length") === 35);
            assert($result === "Moved Temporarily. Redirecting to /");
        });

        it("should return [Moved Temporarily. Redirecting to /] form an empty call", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->accepted = array("text/plain");
            ob_start();
            $response->redirect();
            $result = ob_get_clean();
            assert($response->statusCode === 302);
            assert($response->get("Content-Length") === 35);
            assert($result === "Moved Temporarily. Redirecting to /");
        });

        it("should return [Not Found. Redirecting to /] form an empty call", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->accepted = array("text/plain");
            ob_start();
            $response->redirect(null, 404);
            $result = ob_get_clean();
            assert($response->statusCode === 404);
            assert($response->get("Content-Length") === 27);
            assert($result === "Not Found. Redirecting to /");
        });

        it("should return [<p>Moved Temporarily. Redirecting to <a href=\"/\">/</a></p>]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->accepted = array("text/html");
            ob_start();
            $response->redirect("/");
            $result = ob_get_clean();
            // echo $result;
            assert($response->statusCode === 302);
            assert($response->get("Content-Length") === 58);
            assert($result === "<p>Moved Temporarily. Redirecting to <a href=\"/\">/</a></p>");
        });
    });

    describe("response.location()", function () {

        it("should return [php_require\php_http\Response]", function () {
            $response = new Response();
            $response->request = new Request();
            $result = $response->location("/");
            assert(get_class($result) === "php_require\php_http\Response");
        });

        it("should return [/foo/bar]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->location("/foo/bar");
            assert($response->get("Location") === "/foo/bar");
        });

        it("should return [http://example.com]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->location("http://example.com");
            assert($response->get("Location") === "http://example.com");
        });

        it("should return [/blog/login]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->request->originalUrl = "/blog/login/1";
            $response->location("../");
            assert($response->get("Location") === "/blog/login");
        });

        it("should return [/blog?query=param]", function () {
            $response = new Response();
            $response->request = new Request();
            $response->request->originalUrl = "/blog";
            $response->location("?query=param");
            assert($response->get("Location") === "/blog?query=param");
        });
    });

    describe("response.end()", function () {

        it("should return an empty string []", function () {
            $response = new Response(true);
            ob_start();
            $response->end();
            $result = ob_get_clean();
            assert($result === "");
        });

        it("should return [string]", function () {
            $response = new Response(true);
            ob_start();
            $response->end("string");
            $result = ob_get_clean();
            assert($result === "string");
        });

        it("should throw an error", function () {
            $response = new Response(true);
            $result = false;
            ob_start();
            $response->end();
            try {
                $response->end();
            } catch (Exception $err) {
                $result = true;
            }
            ob_get_clean();
            assert($result === true);
        });
    });

    describe("response.send()", function () {

        it("should return an empty string []", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->send();
            $result = ob_get_clean();
            // echo $result;
            assert($result === "");
        });

        it("should return [404]", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->send(404);
            $result = ob_get_clean();
            // echo $result;
            assert($result === "Not Found");
        });

        it("should return [string]", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->send("string");
            $result = ob_get_clean();
            // echo $result;
            assert($result === "string");
        });

        it("should return [{\"key\":\"val\"}]", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->send(array("key" => "val"));
            $result = ob_get_clean();
            // echo $result;
            assert($result === "{\"key\":\"val\"}");
        });

        it("should return [304]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->fresh = true;
            ob_start();
            $response->send("body");
            $result = ob_get_clean();
            // echo $result;
            assert($result === "");
            assert($response->statusCode === 304);
        });

        it("should return an empty string [] from body", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->method = "HEAD";
            ob_start();
            $response->send("body");
            $result = ob_get_clean();
            // echo $result;
            assert($result === "");
        });
    });

    describe("response.json()", function () {

        it("should return [{\"key\":\"val\"}]", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->json(array("key" => "val"));
            $result = ob_get_clean();
            // echo $result;
            assert($result === "{\"key\":\"val\"}");
            assert($response->get("Content-Type") === "application/json");
        });
    });

    describe("response.jsonp()", function () {

        it("should return [callback && callback({\"key\":\"val\"});]", function () {
            $response = new Response(true);
            $response->request = new Request();
            ob_start();
            $response->jsonp(array("key" => "val"));
            $result = ob_get_clean();
            // echo $result;
            assert($result === "callback && callback({\"key\":\"val\"});");
            assert($response->get("Content-Type") === "text/javascript");
        });

        it("should return [func && func({\"key\":\"val\"});]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->query["jsonp"] = "func";
            ob_start();
            $response->jsonp(array("key" => "val"));
            $result = ob_get_clean();
            // echo $result;
            assert($result === "func && func({\"key\":\"val\"});");
            assert($response->get("Content-Type") === "text/javascript");
        });
    });

    describe("response.type()", function () {

        it("should return [foo]", function () {
            $response = new Response(true);
            $response->type("foo");
            assert($response->get("Content-Type") === "foo");
        });
    });

    describe("response.format()", function () {

        it("should return [default]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $result = "";
            $response->format(array(
                "default" => function () use (&$result) {
                    $result = "default";
                }
            ));
            // echo $result;
            assert($result === "default");
        });

        it("should return [text]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $result = "";
            $response->format(array(
                "text" => function () use (&$result) {
                    $result = "text";
                }
            ));
            // echo $result;
            assert($result === "text");
        });

        it("should return [default]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $result = "";
            $response->format(array(
                "text" => function () use (&$result) {
                    $result = "text";
                },
                "default" => function () use (&$result) {
                    $result = "default";
                }
            ));
            // echo $result;
            assert($result === "default");
        });

        it("should return [html]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->accepted = array("html");
            $result = "";
            $response->format(array(
                "text" => function () use (&$result) {
                    $result = "text";
                },
                "html" => function () use (&$result) {
                    $result = "html";
                },
                "default" => function () use (&$result) {
                    $result = "default";
                }
            ));
            // echo $result;
            assert($result === "html");
        });

        it("should return [html]", function () {
            $response = new Response(true);
            $response->request = new Request();
            $response->request->accepted = array("old", "new", "html", "text");
            $result = "";
            $response->format(array(
                "text" => function () use (&$result) {
                    $result = "text";
                },
                "html" => function () use (&$result) {
                    $result = "html";
                },
                "default" => function () use (&$result) {
                    $result = "default";
                }
            ));
            // echo $result;
            assert($result === "html");
        });
    });
});
