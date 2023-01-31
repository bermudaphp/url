<?php

namespace Bermuda\Utils;

if (PHP_SAPI === 'cli') {
    define('server_schema', '');
    define('server_host', '');
    define('server_schema_is_secure', '');
    define('server_port', '');
} else {
    define('server_schema', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http');

    define('server_host', $_SERVER['SERVER_NAME']);
    define('server_schema_is_secure', server_schema == 'https');
    define('server_port', $_SERVER['SERVER_PORT']);
}

