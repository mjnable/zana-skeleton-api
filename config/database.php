<?php

// SQLite to start with. For MySQL or MariaDB, set the connection in .env, e.g.
// APP_DATABASE__CONNECTIONS__MAIN__DRIVER=mysql, …__HOST, …__DATABASE, …__USERNAME, …__PASSWORD.

return [
    'connections' => [
        'main' => ['driver' => 'sqlite', 'database' => 'var/app.sqlite'],
    ],
];
