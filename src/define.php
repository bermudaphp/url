<?php

namespace Bermuda\Utils;
define("Bermuda\Utils\server_scheme_is_secure", (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    || $_SERVER['SERVER_PORT'] == 443);
const server_scheme = server_scheme_is_secure ? 'https' : 'http';
define("Bermuda\Utils\server_name", server_scheme . '://' . $_SERVER['SERVER_NAME']);
