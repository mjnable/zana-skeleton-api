<?php

// Each test gets a fresh database in memory; tests that need tables run the migrations first.
return [
    'connections' => [
        'main' => ['driver' => 'sqlite', 'database' => ':memory:'],
    ],
];
