<?php

namespace Bermuda\Url;

use Bermuda\Arrayable;

/**
 * @property-read bool isSecure
 * @property-read string scheme
 * @property-read string host
 * @property-read string pass
 * @property-read string port
 * @property-read string query
 * @property-read string path
 * @property-read string fragment
 * @property-read string user
 */
final class Url implements \Stringable, Arrayable
{
    public function __construct(private array $segments)
    {
        foreach ($this->segments as $name => $value) {
            if (!in_array($name, UrlSegment::all)) unset($this->segments[$name]);
        }
    }

    /**
     * @param array|null $segments
     * @return static
     * @throws \RuntimeException
     */
    public static function fromGlobals(array $segments = null): self
    {
        if (!$segments) return new self(parse_url(self::serverUrl()));
        return new self(array_merge(parse_url(self::serverUrl()), $segments));
    }

    /**
     * @return string
     * @throws \RuntimeException
     */
    public static function serverUrl(): string
    {
        if (PHP_SAPI == 'cli') {
            throw new \RuntimeException('PHP cli sapi not supported');
        }
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }

    /**
     * @param string $url
     * @return $this
     */
    public static function parse(string $url): self
    {
        if (($segments = parse_url(urldecode($url))) === false) {
            throw new InvalidArgumentException('Invalid URL passed');
        }

        return new self($segments);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function __get(string $name): string|bool|null
    {
        return match ($name) {
            'isSecure' => strtolower($this->scheme) === 'https',
            UrlSegment::host => $this->segments[UrlSegment::host] ?? null,
            UrlSegment::port => $this->segments[UrlSegment::port] ?? null,
            UrlSegment::user => $this->segments[UrlSegment::user] ?? null,
            UrlSegment::scheme => $this->segments[UrlSegment::scheme] ?? null,
            UrlSegment::pass => $this->segments[UrlSegment::pass] ?? null,
            UrlSegment::path => $this->segments[UrlSegment::path] ?? null,
            UrlSegment::fragment => $this->segments[UrlSegment::fragment] ?? null,
            UrlSegment::query => $this->segments[UrlSegment::query] ?? null,
            'default' => null  
        };
    }

    public function withod(string ... $segments): self
    {
        $data = $this->segments;
        foreach ($segments as $segment) {
            if (array_key_exists($segment, $data)) {
                unset($data[$segment]);
            }
        }

        return new self($data);
    }

    public function withHost(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::host] = $value;

        return new self($segments);
    }

    public function withFragment(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::fragment] = $value;

        return new self($segments);
    }

    public function withUser(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::user] = $value;

        return new self($segments);
    }

    public function withPass(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::pass] = $value;

        return new self($segments);
    }

    public function withPath(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::path] = $value;

        return new self($segments);
    }

    public function withPort(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::port] = $value;

        return new self($segments);
    }

    public function withScheme(string $value): self
    {
        $segments = $this->segments;
        $segments[UrlSegment::scheme] = $value;

        return new self($segments);
    }

    public function withQuery(array|string $value): self
    {
        if (is_array($value)) {
            $value = http_build_query($value);
        }
        
        $segments = $this->segments;
        $segments[UrlSegment::query] = $value;

        return new self($segments);
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
        return $this->toString();
    }

    public function toString(): string
    {
        return self::build($this->segments);
    }

    /**
     * @param array $segments
     * @return string
     */
    public static function build(array $segments = []): string
    {
        if (PHP_SAPI == 'cli') {
            throw new \RuntimeException('PHP cli sapi not supported');
        }

        $url = ($segments[UrlSegment::scheme] ?? server_scheme) . '://';

        if (!empty($segments[UrlSegment::user])) {
            $url .= $segments[UrlSegment::user] . ':' . $segments[UrlSegment::pass] . '@';
        }

        $url .= ($segments[UrlSegment::host] ?? $_SERVER['SERVER_NAME']);

        if (!empty($segments[UrlSegment::port])) {
            $url .= ':' . $segments[UrlSegment::port];
        }

        if (!empty($segments[UrlSegment::path])) {
            $url .= '/' . trim($segments[UrlSegment::path], '/');
        }

        if (!empty($segments[UrlSegment::query])) {
            if ($url[strlen($url)-1] != '/') $url .= '/';

            $url .= '?';

            if (is_array($segments[UrlSegment::query])) {
                $url .= http_build_query($segments[UrlSegment::query]);
            } else {
                $url .= $segments[UrlSegment::query];
            }
        }

        if (!empty($segments[UrlSegment::fragment])) {
            $url .= '#' . $segments[UrlSegment::fragment];
        }

        return $url;
    }
}
