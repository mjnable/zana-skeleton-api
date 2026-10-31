<?php

declare(strict_types=1);

use Zana\Database\Migration\Migration;
use Zana\Database\Schema\Blueprint;
use Zana\Database\Schema\Schema;

return new class () extends Migration {
    public function up(Schema $schema): void
    {
        $schema->create('users', static function (Blueprint $t): void {
            $t->uuid('id')->primary();
            $t->integer('version');
            $t->string('email', 254)->unique();
            $t->string('name', 200);
            $t->string('password_hash', 255);
            $t->text('roles');
        });
    }

    public function down(Schema $schema): void
    {
        $schema->drop('users');
    }
};
