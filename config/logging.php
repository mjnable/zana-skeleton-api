<?php

// Warnings and errors, server errors included, go to var/log/app.log: on shared hosting, open it in
// the file manager when a page shows the error page. It rotates at 10 MB and keeps 5 old files.
// APP_LOGGING__CHANNELS__APP__LEVEL=debug on a server logs more.

return [
    'default' => 'app',
    'channels' => [
        'app' => [
            'handler' => 'stream',
            'path' => 'var/log/app.log',
            'level' => 'warning',
            'max_size' => '10M',
            'max_files' => 5,
        ],
    ],
];
