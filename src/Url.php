<?php

namespace Bermuda\Utils;

use Bermuda\Arrayable;

/**
 * @property-read bool isSecure
 * @property-read string asString
 */
final class Url implements \Stringable, Arrayable
{
    public const host = 'host';
    public const schema = 'schema';
    public const query = 'query';
    public const user = 'user';
    public const pass = 'pass';
    public const port = 'port';
    public const path = 'path';
    public const fragment = 'fragment';

    public function __construct(
        public ?string $scheme = null, public ?string $user = null,
        public ?string $pass = null, public ?string $host = null,
        public ?string $port = null, public ?string $path = null,
        public ?array $query = null, public ?string $fragment = null
    ){
        $this->scheme = $scheme ?? server_schema;
        $this->host = $host ?? $_SERVER['SERVER_NAME'];
    }

    public static function fromGlobals(array $segments): self
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            $path = trim((explode('?', $_SERVER['REQUEST_URI']))[0], '/');
        }

        return new self(path: $segments[self::path] ?? $path, query: $segments[self::query] ?? $_GET);
    }

    /**
     * @param string $url
     * @return $this
     */
    public static function fromString(string $url): self
    {
        if (($segments = parse_url($url)) === false) {
            throw new InvalidArgumentException('Invalid URL passed');
        }

        return self::fromArray($segments);
    }

    /**
     * @param array $segments
     * @return $this
     */
    public static function fromArray(array $segments): self
    {
        return new self(
            $segments[self::schema] ?? null, $segments[self::user] ?? null,
            $segments[self::pass] ?? null, $segments[self::host] ?? null,
            $segments[self::port] ?? null, $segments[self::pass] ?? null,
            $segments[self::query] ?? null, $segments[self::fragment] ?? null
        );
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function __get(string $name): string|bool|null
    {
        return match ($name) {
            'isSecure' => strtolower($this->scheme) === 'https',
            'asString' => (string) $this,
            'default' => null  
        };
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter(get_object_vars($this), static fn($v) => $v !== null);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return self::build($this->toArray());
    }

    /**
     * @param array $segments
     * @return string
     */
    public static function build(array $segments = []): string
    {
        $url = ($segments[self::schema] ?? server_schema) . '://';

        if (!empty($segments[self::user])) {
            $url .= $segments[self::user] . ':' . $segments[self::pass] . '@';
        }

        $url .= ($segments[self::host] ?? $_SERVER['SERVER_NAME']);

        if (!empty($segments[self::port])) {
            $url .= ':' . $segments[self::port];
        }

        if (!empty($segments[self::path])) {
            $url .= '/' . trim($segments[self::path], '/');
        }

        if (!empty($segments[self::query])) {
            $url .= '?'. http_build_query($segments[self::query]);
        }

        if (!empty($segments[self::fragment])) {
            $url .= '#' . $segments[self::fragment];
        }

        return $url;
    }
}
