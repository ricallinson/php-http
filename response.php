<?php
namespace php_require\php_http;

class Response {

    public $STATUS_CODES = array(
        100 => "Continue",
        101 => "Switching Protocols",
        102 => "Processing",                 // RFC 2518, obsoleted by RFC 4918
        200 => "OK",
        201 => "Created",
        202 => "Accepted",
        203 => "Non-Authoritative Information",
        204 => "No Content",
        205 => "Reset Content",
        206 => "Partial Content",
        207 => "Multi-Status",               // RFC 4918
        300 => "Multiple Choices",
        301 => "Moved Permanently",
        302 => "Moved Temporarily",
        303 => "See Other",
        304 => "Not Modified",
        305 => "Use Proxy",
        307 => "Temporary Redirect",
        400 => "Bad Request",
        401 => "Unauthorized",
        402 => "Payment Required",
        403 => "Forbidden",
        404 => "Not Found",
        405 => "Method Not Allowed",
        406 => "Not Acceptable",
        407 => "Proxy Authentication Required",
        408 => "Request Time-out",
        409 => "Conflict",
        410 => "Gone",
        411 => "Length Required",
        412 => "Precondition Failed",
        413 => "Request Entity Too Large",
        414 => "Request-URI Too Large",
        415 => "Unsupported Media Type",
        416 => "Requested Range Not Satisfiable",
        417 => "Expectation Failed",
        418 => "I\"m a teapot",              // RFC 2324
        422 => "Unprocessable Entity",       // RFC 4918
        423 => "Locked",                     // RFC 4918
        424 => "Failed Dependency",          // RFC 4918
        425 => "Unordered Collection",       // RFC 4918
        426 => "Upgrade Required",           // RFC 2817
        428 => "Precondition Required",      // RFC 6585
        429 => "Too Many Requests",          // RFC 6585
        431 => "Request Header Fields Too Large",// RFC 6585
        500 => "Internal Server Error",
        501 => "Not Implemented",
        502 => "Bad Gateway",
        503 => "Service Unavailable",
        504 => "Gateway Time-out",
        505 => "HTTP Version Not Supported",
        506 => "Variant Also Negotiates",    // RFC 2295
        507 => "Insufficient Storage",       // RFC 4918
        509 => "Bandwidth Limit Exceeded",
        510 => "Not Extended",               // RFC 2774
        511 => "Network Authentication Required" // RFC 6585
    );
    
    private $pathlib = null;

    private $testing = false;

    private $headersSent = false;

    private $headers = array();

    public $statusCode = 200;

    public $charset = "utf-8";

    public $locals = null;

    public $renderer = array();

    public $request = null;

    public function __construct($testing = false) {
        $this->testing = $testing;
        $this->pathlib = new \php_require\php_path\Path();
    }

    public function status($code) {
        $this->statusCode = $code;
        return $this;
    }

    public function set($field, $value) {
        $this->headers[strtolower($field)] = $value;
        return $this;
    }

    public function get($field) {
        $key = strtolower($field);
        if (!isset($this->headers[$key])) {
            return null;
        }
        return $this->headers[$key];
    }

    public function removeHeader($field) {
        $key = strtolower($field);
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }
        return $this;
    }

    public function cookie($name, $value, $options = null) {
        if (!headers_sent()) {
            setcookie($name, $value, 0);
        }
        return $this;
    }

    public function clearCookie($name, $options = null) {
        if (!headers_sent()) {
            setcookie($name, "", time() - 3600);
        }
        return $this;
    }

    public function redirect($url = "", $status = null) {

        $app = null;
        $req = $this->request;
        $head = "HEAD" === $req->method;
        $status = $status ? $status : 302;
        $statusCodes = $this->STATUS_CODES;
        $body = "";

        if ($url === "") {
            $url = $req->getServerVar("REQUEST_URI", "/");
        }

        // Set location header
        $this->location($url);
        $url = $this->get("Location");

        // Support text/{plain,html} by default
        $this->format(array(
            "text/plain" => function () use (&$body, $url, $status, $statusCodes) {
                $body = $statusCodes[$status] . ". Redirecting to " . $url;
            },

            "text/html" => function () use (&$body, $url, $status, $statusCodes) {
                $body = "<p>" . $statusCodes[$status] . ". Redirecting to <a href=\"" . $url . "\">" . $url . "</a></p>";
            },

            "default" => function () use (&$body) {
                $body = "";
            }
        ));

        // Respond
        $this->statusCode = $status;
        $this->set("Content-Length", strlen($body));
        $this->end($head ? null : $body);
    }

    /*
        This method needs more work.
    */

    public function location($url) {

        $req = $this->request;

        // Find url in a map (to be added later)
        // $url = isset($url) ? $this->mapping[$url] : $url;

        // relative to path
        if (strpos($url, "://") === false && $url[0] !== "/") {
            $pathParts = explode("?", $req->originalUrl);
            $path = $pathParts[0];
            if ($url[0] === ".") {
                $url = $this->pathlib->join($path, $url);
            } else {
                $url = $path . $url;
            }
        }

        // Respond
        $this->set("Location", $url);
        return $this;
    }

    public function end($data = "", $encoding = null) {

        if ($this->headersSent) {
            throw new \Exception("Cannot call Response.end() more than once.");
        }

        if (!headers_sent()) {
            foreach ($this->headers as $field => $value) {
                header($field . ": " . $value, true);
            }
        }

        $this->headersSent = true;

        echo($data);

        if ($this->testing) {
            return;
        }

        exit();
    }

    public function send($body = "", $status = null) {

        $req = $this->request;
        $head = "HEAD" === $req->method;
        $len = 0;

        switch (gettype($body)) {
            case "integer":
                // If we have a content type do nothing, otherwise use type "text"
                $this->get("Content-Type") ? null : $this->type("txt");
                $this->statusCode = $body;
                $body = $this->STATUS_CODES[$body];
                break;
            case "string":
                if (!$this->get("Content-Type")) {
                    $this->charset = $this->charset ? $this->charset : "utf-8";
                    $this->type("html");
                }
                break;
            case "array":
                return $this->json($body);
        }

        // populate Content-Length
        if ($body && !$this->get("Content-Length")) {
            $len = strlen($body);
            $this->set("Content-Length", $len);
        }

        // ETag support
        // TODO: W/ support
        if ($len > 1024 && "GET" == $req->method) {
            if (!$this->get("ETag")) {
                // $this->set("ETag", etag(body));
            }
        }

        // freshness
        if ($req->fresh) {
            $this->statusCode = 304;
        }

        // strip irrelevant headers
        if ($this->statusCode === 204 || $this->statusCode === 304) {
            $this->removeHeader("Content-Type");
            $this->removeHeader("Content-Length");
            $this->removeHeader("Transfer-Encoding");
            $body = "";
        }

        // respond
        $this->end($head ? null : $body);
        return $this;
    }

    public function json($array, $status = null) {

        // content-type
        $this->get("Content-Type") ? null : $this->set("Content-Type", "application/json");

        return $this->send(json_encode($array), $status);
    }

    public function jsonp($array, $status = null) {
        
        $body = json_encode($array);
        $body = str_replace("\u2028", "\\u2028", $body);
        $body = str_replace("\u2029", "\\u2029", $body);

        // get callback name
        $callback = isset($this->request->query["jsonp"]) ? $this->request->query["jsonp"] : "callback";

        // content-type
        $this->set("Content-Type", "text/javascript");
        $this->charset = $this->charset ? $this->charset : "utf-8";

        // cleanup the callback name
        $cb = str_replace("/[^\[\]\w$.]/g", "", $callback);
        $body = $cb . " && " . $cb . "(" . $body . ");";

        return $this->send($body);
    }

    public function type($type) {
        return $this->set("Content-Type", $type);
    }

    public function format($array) {
        
        $req = $this->request;
        $next = null;
        $fn = null;

        if (isset($array["default"])) {
            $fn = $array["default"];
            unset($array["default"]);
        }

        $keys = array_keys($array);

        $key = $req->accepts($keys);

        $this->set('Vary', 'Accept');

        if ($key) {
            // use the found type
            $this->set('Content-Type', $key);
            $fn = $array[$key];
            $fn();
        } else if ($fn) {
            // use default
            $fn();
        } else if (isset($array[$keys[count($keys) - 1]])) {
            // use the last item in the array
            $fn = $array[$keys[count($keys) - 1]];
            $fn();
        }

        return $this;
    }

    public function attachment($filename = null) {
        //
    }

    public function sendfile($path, $options = null, $fn = null) {
        //
    }

    public function download($path, $filename = null, $fn = null) {
        //
    }

    public function links($links) {
        //
    }

    public function render($filename, $data = null, $callback = null) {

        $that = $this;

        if (!$callback) {
            $callback = function ($error, $string) use (&$that) {
                $that->send($string);
            };
        }

        $type = explode(".", $filename, 2);
        $type = "." . $type[1];

        if (!isset($this->renderer[$type])) {
            throw new \Exception("Renderer for type [" . $type . "] not set");
        }

        $renderer = $this->renderer[$type];

        $renderer($filename, $data, $callback);
    }
}

/*
    Return a new Response
*/

$module->exports = new Response();

$module->exports->request = $require("./request");

