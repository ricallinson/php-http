<?php
use php_require\request\Response;

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

    describe("response", function () {

        it("should return", function () {
            assert(true);
        });
    });
});
