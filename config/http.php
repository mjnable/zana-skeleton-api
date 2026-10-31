<?php

use Zana\Http\SecurityHeadersMiddleware;
use Zana\Http\TrustedProxiesMiddleware;
use Zana\Security\Middleware\AuthenticationMiddleware;

return [
    // Every request: the client address behind proxies, security headers, and the bearer token
    // (Authorization: Bearer …), which sets the user when there is one.
    'middleware' => [TrustedProxiesMiddleware::class, SecurityHeadersMiddleware::class, AuthenticationMiddleware::class],

    // Errors are always problem JSON (RFC 9457), whatever the client accepts.
    'json_errors' => true,

    // Behind a CDN or a load balancer, list its addresses (CIDR ranges work) so that the client
    // address and HTTPS are read from its headers.
    'trusted_proxies' => [],
];
