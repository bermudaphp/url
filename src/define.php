<?php

namespace Bermuda\Utils;

const server_schema_is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
  || $_SERVER['SERVER_PORT'] == 443;
const server_schema = server_schema_is_secure ? 'https' : 'http';
const server_name = server_schema . '://' . $_SERVER['SERVER_NAME'];
