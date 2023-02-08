# Install
```bash
composer require bermudaphp/url
````
# Usage
```php
$url = Url::parse('http://username:password@hostname:9090/path?arg=value#anchor');

^ Bermuda\Url\Url {#231 ▼
  -segments: array:8 [▼
    "scheme" => "http"
    "host" => "hostname"
    "port" => 9090
    "user" => "username"
    "pass" => "password"
    "path" => "/path"
    "query" => "arg=value"
    "fragment" => "anchor"
  ]
}

$url->withod('fragment', 'path')->toString(); // "http://username:password@hostname:9090/?arg=value"

$currentUrl = Url::fromGlobals()->toString(); // https://github.com/bermudaphp/url
$currentUrl = $currentUrl->withHost('new-hostname.com');

$currentUrl->host; // 'new-hostname.com'

$currentUrl->toString(); // 'https://new-hostname.com/bermudaphp/url'
$currentUrl->toArray();

^ array:1 [▼
  "segments" => array:3 [▼
    "scheme" => "https"
    "host" => "new-hostname.com"
    "path" => "/bermudaphp/url"
  ]
]
````
