<?php
namespace php_require\php_http;

class Request {

    public $config = array();

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

        $this->originalUrl = $this->getServerVar("REQUEST_URI", "/");

        $this->accepted = explode(",", $this->getServerVar("HTTP_ACCEPT"));

        $this->acceptedCharsets = $this->getServerVar("HTTP_ACCEPT_CHARSET");

        $this->acceptedLanguages = $this->getServerVar("HTTP_ACCEPT_LANGUAGE");

        $this->body = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null;

        $this->cookies = $_COOKIE;

        $this->ip = $this->getServerVar("SERVER_ADDR");

        $urlparts = explode("?", $this->originalUrl);

        $this->path = rtrim($urlparts[0], "/");

        $this->host = $this->getServerVar("SERVER_NAME");

        $protocolParts = explode("/", $this->getServerVar("SERVER_PROTOCOL"));

        $this->protocol = strtolower($protocolParts[0]);

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

    public function getServerVar($key, $default = null) {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : $default;
    }

    public function cookie($name, $array = array(), $default = null) {
        $key = strtolower($name);
        if (!isset($this->cookies[$key])) {
            return $this->find($name, $array, $default);
        }
        return $this->cookies[$key];
    }

    public function param($name, $array = array(), $default = null) {
        $key = strtolower($name);
        if (!isset($this->query[$key])) {
            return $this->find($name, $array, $default);
        }
        return $this->query[$key];
    }

    public function get($field, $array = array(), $default = null) {
        $key = strtolower($field);
        if (!isset($this->headers[$key])) {
            return $this->find($field, $array, $default);
        }
        return $this->headers[$key];
    }

    public function find($name, $array = array(), $default = null) {
        $key = strtolower($name);
        if (gettype($array) !== "array") {
            $default = $array;
            $array = array();
        }
        if (!isset($array[$key])) {
            return $default;
        }
        return $array[$key];
    }

    public function accepts($types) {

        foreach ($this->accepted as $type) {
            if (in_array($type, $types)) {
                return $type;
            }
        }

        return null;
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
            $this->config[$key] = $value;
        } else {
            if (!isset($this->config[$key])) {
                return null;
            }
            return $this->config[$key];
        }
    }
}

$module->exports = new Request();
