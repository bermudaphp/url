<?php

namespace Bermuda\Utils;
define("Bermuda\Utils\server_schema_is_secure", (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || $_SERVER['SERVER_PORT'] == 443);
const server_schema = server_schema_is_secure ? 'https' : 'http';
define("Bermuda\Utils\server_name", server_schema . '://' . $_SERVER['SERVER_NAME']);
