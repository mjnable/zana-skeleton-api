<?php

// The secret signs the bearer tokens: it comes from .env (APP_SECURITY__SECRET), never from this file.

return [
    // The security rules of `php bin/console check` are errors, not warnings: a route that does not say
    // who may reach it fails check, CI and release:build. Keep it on (2.21, FE-68).
    'strict' => true,

    // How long a token from POST /api/tokens is valid, in seconds.
    'token_lifetime' => 3600,

    // Roles and the permissions they grant; check them with #[RequiresPermission('…')].
    'roles' => [
        'admin' => ['*'],
    ],
];
