<?php

use Zana\Validation\ValidationBehavior;

return [
    // Run around every command and query, in order. ValidationBehavior refuses a message whose
    // constraint attributes fail, whatever sent it: an API action, a console command or a queue job.
    'behaviors' => [ValidationBehavior::class],
];
