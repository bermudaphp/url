<?php

namespace Bermuda\Utils;

if (PHP_SAPI === 'cli') {
    define('server_scheme', '');
    define('server_host', '');
    define('server_scheme_is_secure', '');
    define('server_port', '');
} else {
    define('server_scheme', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http');

    define('server_host', $_SERVER['SERVER_NAME']);
    define('server_scheme_is_secure', server_schema == 'https');
    define('server_port', $_SERVER['SERVER_PORT']);
}

