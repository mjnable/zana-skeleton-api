<?php

declare(strict_types=1);

// For the tests only (APP_ENV=testing), so that they run without .env, e.g. in CI.
// Every other environment takes the secret from .env or APP_SECURITY__SECRET.
return [
    'secret' => 'testing-only-secret-never-used-outside-the-tests-0123456789',
];
