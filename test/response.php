<?php
/*
    Create a MockModule to load our module into for testing.
*/

if (!class_exists("MockModule")) {
    class MockModule {
        public $exports = array();
    }
}
$module = new MockModule();

/*
    Now we "require()" the file to test.
*/

require(__DIR__ . "/../lib/response.php");

/*
    Now we test it.
*/

describe("php-http", function () {
    it("should return", function () {
        assert(true);
    });
});
