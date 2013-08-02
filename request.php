<?php
namespace php_require\request;

class Request {

    /**/

    public $headers = array();

    /**/

    public $method = null;

    public $params = null;

    /**/

    public $query = null;

    /**/

    public $body = null;

    public $files = null;

    public $route = null;

    /**/

    public $cookies = null;

    public $signedCookies = null;

    /**/

    public $accepted = null;

    /**/

    public $ip = null;

    public $ips = null;

    /**/

    public $path = null;

    /**/

    public $host = null;

    public $fresh = null;

    public $stale = null;

    /**/

    public $xhr = null;

    /**/

    public $protocol = null;

    /**/

    public $secure = null;

    public $subdomains = null;

    /**/

    public $originalUrl = null;

    /**/

    public $acceptedLanguages = null;

    /**/

    public $acceptedCharsets = null;

    /*
        All of these attributes need to be lazy loaded as used.
    */

    public function __construct() {

        $this->method = strtoupper($this->getServerVar("REQUEST_METHOD"));

        $this->originalUrl = $this->getServerVar("REQUEST_URI");

        $this->accepted = $this->getServerVar("HTTP_ACCEPT");

        $this->acceptedCharsets = $this->getServerVar("HTTP_ACCEPT_CHARSET");

        $this->acceptedLanguages = $this->getServerVar("HTTP_ACCEPT_LANGUAGE");

        $this->body = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null;

        $this->cookies = $_COOKIE;

        $this->ip = $this->getServerVar("SERVER_ADDR");

        $this->path = rtrim(explode("?", $this->originalUrl)[0], "/");

        $this->host = $this->getServerVar("SERVER_NAME");

        $this->protocol = strtolower(explode("/", $this->getServerVar("SERVER_PROTOCOL"))[0]);

        $this->secure = $this->protocol === "https";

        if ($this->method === "GET") {
            $this->query = $_GET;
        } else if ($this->method === "POST") {
            $this->query = $_POST;
        } else {
            $this->query = $_REQUEST;
        }

        if (function_exists("getallheaders")) {
            foreach (getallheaders() as $key => $value) {
                $this->headers[strtolower($key)] = $value;
            }
        }

        $this->xhr = $this->get('X-Requested-With') ? 'xmlhttprequest' === $this.get('X-Requested-With').toLowerCase() : false;
    }

    public function getServerVar($key) {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    }

    public function cookie($name) {
        $key = strtolower($name);
        if (!isset($this->cookies[$key])) {
            return null;
        }
        return $this->cookies[$key];
    }

    public function param($name) {
        $key = strtolower($name);
        if (!isset($this->query[$key])) {
            return null;
        }
        return $this->query[$key];
    }

    public function get($field) {
        $key = strtolower($field);
        if (!isset($this->headers[$key])) {
            return null;
        }
        return $this->headers[$key];
    }

    public function accepts($types) {
        return $types[0];
    }

    public function is($type) {
        //
    }

    public function acceptsCharset($charset) {
        //
    }

    public function acceptsLanguage($lang) {
        //
    }

    public function cfg($name, $value=null) {
        $key = strtolower($name);
        if ($value) {
            $this->cfg[$key] = $value;
        } else {
            if (!isset($this->cfg[$key])) {
                return null;
            }
            return $this->cfg[$key];
        }
    }
}

$module->exports = new Request();
