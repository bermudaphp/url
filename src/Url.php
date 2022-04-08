<?php

namespace Bermuda\Utils;

/**
 * @property-read bool isSecure
 */
final class Url implements \Stringable
{
    public function __construct(public ?string $schema = null, public ?string $user = null,
        public ?string $pass = null, public ?string $host = null, public ?string $port = null,
        public ?string $path = null, public ?array $query = null, public ?string $fragment = null
    ){
        $this->schema = $schema ?? self::getCurrentSchema();
        $this->host = $host ?? $_SERVER['SERVER_NAME'];
    }

    public static function createCurrent(): self
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
        $array = parse_url($url);

        if ($array === false) {
            throw new InvalidArgumentException('Invalid URL passed');
        }

        return new self(
            $array['scheme'] ?? null, $array['user'] ?? null,
            $array['pass'] ?? null, $array['host'] ?? null,
            $array['port'] ?? null, $array['pass'] ?? null,
            $array['query'] ?? null, $array['fragment'] ?? null
        );
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function __get(string $name): ?string
    {
        return match ($name) {
          'isSecure' => $schema !== null ? strcasecmp($schema, 'https') : self::isSecure(),
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
        $url = ($segments['schema'] ?? self::getCurrentSchema()) . '://';

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

    /**
     * @return string
     */
    public static function getCurrentSchema(): string
    {
        return self::isSecure() ? 'https' : 'http';
    }

    /**
     * @return string
     */
    public static function getCurrentDomain(): string
    {
        return self::getCurrentSchema() . '://' . $_SERVER['SERVER_NAME'];
    }

    /**
     * @return bool
     */
    public static function isSecure(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            || $_SERVER['SERVER_PORT'] == 443;
    }
}
