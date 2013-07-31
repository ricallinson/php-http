<?php

$module = new stdClass();
$require = function () {};

/*
    Now we "require()" the file to test.
*/

require(__DIR__ . "/../response.php");

/*
    Now we test it.
*/

describe("php-http", function () {
    it("should return", function () {
        assert(true);
    });
});
