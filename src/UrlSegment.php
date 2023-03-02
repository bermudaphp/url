<?php

namespace Bermuda\Stdlib;

final class UrlSegment
{
    public const host = 'host';
    public const scheme = 'scheme';
    public const query = 'query';
    public const user = 'user';
    public const pass = 'pass';
    public const port = 'port';
    public const path = 'path';
    public const fragment = 'fragment';
    public const all = [
        self::host, self::scheme,
        self::query, self::user,
        self::pass, self::port,
        self::path, self::fragment
    ];
}
