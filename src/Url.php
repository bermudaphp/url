<?php

namespace Bermuda\Utils;

/**
 * @property-read bool isSecure
 * @property-read string asString
 */
final class URL implements \Stringable
{
    public function __construct(public ?string $scheme = null, public ?string $user = null,
        public ?string $pass = null, public ?string $host = null, public ?string $port = null,
        public ?string $path = null, public ?array $query = null, public ?string $fragment = null
    ){
        $this->scheme = $scheme ?? server_scheme;
        $this->host = $host ?? $_SERVER['SERVER_NAME'];
    }

    public static function createFromServerRequestUri(): self
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            $path = trim((explode('?', $_SERVER['REQUEST_URI']))[0], '/');
        }

        return new self(path: $path, query: $_GET);
    }

    /**
     * @param string $url
     * @return $this
     */
    public function createFromString(string $url): self
    {
        $segments = parse_url($url);
        if ($segments === false) {
            throw new InvalidArgumentException('Invalid URL passed');
        }

        return self::createFromArray($segments);
    }

    /**
     * @param array $segments
     * @return $this
     */
    public function createFromArray(array $segments): self
    {
        return new self(
            $segments['scheme'] ?? null, $segments['user'] ?? null,
            $segments['pass'] ?? null, $segments['host'] ?? null,
            $segments['port'] ?? null, $segments['pass'] ?? null,
            $segments['query'] ?? null, $segments['fragment'] ?? null
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
        $url = ($segments['scheme'] ?? self::getCurrentSchema()) . '://';

        if (!empty($segments['user'])) {
            $url .= $segments['user'] . ':' . $segments['pass'] . '@';
        }

        $url .= ($segments['host'] ?? $_SERVER['SERVER_NAME']);

        if (!empty($segments['port'])) {
            $url .= ':' . $segments['port'];
        }

        if (!empty($segments['path'])) {
            $url .= '/' . trim($segments['path'], '/');
        }

        if (!empty($segments['query'])) {
            $url .= '?'. http_build_query($segments['query']);
        }

        if (!empty($segments['fragment'])) {
            $url .= '#' . $segments['fragment'];
        }

        return $url;
    }
}
