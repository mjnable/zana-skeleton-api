<?php

use Zana\Http\CorsMiddleware;
use Zana\Http\SecurityHeadersMiddleware;
use Zana\Http\TrustedProxiesMiddleware;
use Zana\Security\Middleware\AuthenticationMiddleware;

return [
    // Every request: the client address behind proxies, cross-origin reads by the sites listed in
    // cors (none until you list them), security headers, and the bearer token (Authorization: Bearer …),
    // which sets the user when there is one.
    'middleware' => [TrustedProxiesMiddleware::class, CorsMiddleware::class, SecurityHeadersMiddleware::class, AuthenticationMiddleware::class],

    // Errors are always problem JSON (RFC 9457), whatever the client accepts.
    'json_errors' => true,

    // Behind a CDN or a load balancer, list its addresses (CIDR ranges work) so that the client
    // address and HTTPS are read from its headers.
    'trusted_proxies' => [],

    // The sites whose pages call this API from a browser (2.32), each as https://host; "*" for any site,
    // without credentials. Leave it empty for apps and servers, which need no CORS.
    'cors' => [
        'origins' => [],
        // 'credentials' => false,   // true only if those pages send cookies
    ],
];
