<?php

$module = new stdClass();

/*
    Now we "require()" the file to test.
*/

require(__DIR__ . "/../request.php");

/*
    Now we test it.
*/

describe("php-http", function () {
    it("should return", function () {
        assert(true);
    });
});
