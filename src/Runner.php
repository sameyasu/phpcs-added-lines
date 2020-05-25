<?php

namespace PhpcsAddedLines;

/**
 * Runner class
 */
class Runner
{
    use Logger\StderrLoggerTrait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initLogger();
    }

    /**
     * Runs PHPCS and then filters out unmodified lines.
     *
     * @return int exitCode
     */
    public function run(): int
    {
        $argv = [
            $_SERVER['argv'][0],
            '--no-colors',
            '--report=json',
        ];
        // inherit arguments
        foreach (array_slice($_SERVER['argv'], 1) as $v) {
            if (!$this->matchesIgnoringArgs($v)) {
                $argv[] = $v;
            }
        }
        $this->logger->debug('Runtime Arguments', ['argv' => $argv]);

        // Override PHPCS runtime arguments
        $_SERVER['argv'] = $argv;

        ob_start();
        $runner = new \PHP_CodeSniffer\Runner();
        $exitCode = $runner->runPHPCS();
        $result = ob_get_clean();

        $this->logger->debug('Result', ['exitCode' => $exitCode]);

        $json = json_decode($result, true);
        $errorCode = json_last_error();
        if ($errorCode !== JSON_ERROR_NONE) {
            $this->logger->error('Failed to run PHPCS', [
                'json_last_erorr' => $errorCode,
                'json_last_error_msg' => json_last_error_msg()
            ]);
            $this->logger->debug('PHPCS result', [
                'argv' => $argv,
                'stdout' => $result
            ]);
        } else {
            $this->logger->debug('PHPCS ran successfully');
        }

        echo $result;
        return $exitCode;
    }

    /**
     * Whether it matches ignoring arguments.
     *
     * @param string $arg
     * @return bool
     */
    protected function matchesIgnoringArgs(string $arg): bool
    {
        if ($arg === '-') {
            // ignores reading from stdin
            return true;
        } elseif ($arg === '-a') {
            // ignores interactive mode
            return true;
        } elseif ($this->startsWith($arg, '--report')) {
            // ignores reporting options
            return true;
        }
        return false;
    }

    /**
     * Whether "$text" string starts with "$part" string.
     *
     * @param string $text
     * @param string $part
     * @return bool
     */
    private function startsWith(string $text, string $part)
    {
        return strlen($text) >= strlen($part) && strpos($text, 0, strlen($part)) === $part;
    }
}
