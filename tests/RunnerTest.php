<?php

declare(strict_types=1);

namespace PhpcsAddedLines;

use PHPUnit\Framework\TestCase;

class RunnerTest extends TestCase
{
    private $runner;

    public function setUp(): void
    {
        $this->runner = new class extends Runner {
            public function callMatchesIgnoringArgs(string $arg): bool
            {
                return $this->matchesIgnoringArgs($arg);
            }
        };
    }

    public function testMatchesIgnoringArgs1(): void
    {
        $this->assertTrue($this->runner->callMatchesIgnoringArgs('-'));
    }

    public function testMatchesIgnoringArgs2(): void
    {
        $this->assertTrue($this->runner->callMatchesIgnoringArgs('-a'));
    }

    public function testMatchesIgnoringArgs3(): void
    {
        $this->assertTrue($this->runner->callMatchesIgnoringArgs('--report'));
    }

    public function testMatchesIgnoringArgs4(): void
    {
        $this->assertTrue($this->runner->callMatchesIgnoringArgs('--report-file=json'));
    }

    public function testMatchesIgnoringArgs5(): void
    {
        $this->assertFalse($this->runner->callMatchesIgnoringArgs('--help'));
    }

    public function testMatchesIgnoringArgs6(): void
    {
        $this->assertFalse($this->runner->callMatchesIgnoringArgs(''));
    }
}
