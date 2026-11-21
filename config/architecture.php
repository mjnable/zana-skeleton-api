<?php

// The dependency rules `check` enforces between the modules (docs: cookbook/modules.md).
return [
    // A shared kernel (2.30): modules' Contracts that every layer of every module may use, Domain included, for values
    // that belong to all of them, such as money or a clock. Only values: each type here couples every module to it.
    // 'shared_kernel' => ['Modules\Shared\Contracts'],
];
