<?php

declare(strict_types=1);

namespace App\Tests;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;

/**
 * AGENTS.md tells AI coding agents the rules of this application and links
 * to the documentation of its Zana version. After upgrading Zana, this test
 * fails until the file is written again: php bin/console make:agents --force
 * (then add back any section of your own).
 */
final class AgentsFileTest extends TestCase
{
    public function testTheInstructionsMatchTheInstalledZanaVersion(): void
    {
        $agents = (string) file_get_contents(dirname(__DIR__) . '/AGENTS.md');
        preg_match('/^v?(\d+\.\d+)/', (string) InstalledVersions::getPrettyVersion('mj-zana/core'), $installed);

        $this->assertStringContainsString("This is a Zana {$installed[1]} application", $agents, 'Run: php bin/console make:agents --force');
        $this->assertStringContainsString("https://github.com/mjnable/zana-docs/tree/{$installed[1]}", $agents);
        $this->assertSame('@AGENTS.md', trim((string) file_get_contents(dirname(__DIR__) . '/CLAUDE.md')));
    }
}
