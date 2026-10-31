<?php

// The secret signs the bearer tokens: it comes from .env (APP_SECURITY__SECRET), never from this file.

return [
    // How long a token from POST /api/tokens is valid, in seconds.
    'token_lifetime' => 3600,

    // Roles and the permissions they grant; check them with #[RequiresPermission('…')].
    'roles' => [
        'admin' => ['*'],
    ],
];
