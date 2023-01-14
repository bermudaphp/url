<?php

namespace Bermuda\Utils;

define('server_schema', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
|| $_SERVER['SERVER_PORT'] == 443 ? 'https' : 'http');

define('server_host', $_SERVER['SERVER_NAME']);
define('server_schema_is_secure', server_schema == 'https');
define('server_port', $_SERVER['SERVER_PORT']);
