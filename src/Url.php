<?php

namespace Bermuda\Paginator;

use Bermuda\Url\Url;
use Bermuda\Url\UrlSegment;
use Psr\Http\Message\ServerRequestInterface;

class Query implements QueryInterface
{
    public function __construct(
        public readonly Url $url,
        protected array $queryParams = [],
    ) {
    }

    public static function fromGlobals(): static
    {
        return new static(Url::fromGlobals()->withod('query'), $_GET);
    }

    public function __toString(): string
    {
        return http_build_query($this->queryParams);
    }

    public function toArray(): array
    {
        return $this->queryParams;
    }

    public function getIterator(): \Generator
    {
        foreach ($this->queryParams as $prop => $value) yield $prop => $value;
    }

    public function with(string $name, mixed $value): QueryInterface
    {
        $copy = clone $this;
        $copy->queryParams[$name] = $value;

        return $copy;
    }

    public function withod(string $name): QueryInterface
    {
        $copy = clone $this;
        unset($copy->queryParams[$name]);

        return $copy;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->queryParams[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->queryParams);
    }

    public function toString(): string
    {
        return $this->url->withQuery($this->queryParams)->toString();
    }

    public static function fromRequest(ServerRequestInterface $request): static
    {
        return new static(new Url([
            UrlSegment::scheme => $request->getUri()->getScheme(),
            UrlSegment::host => $request->getUri()->getHost()
        ]), $request->getQueryParams());
    }
}
