<?php

namespace PhpcsAddedLines;

/**
 * Runner class
 */
class Runner
{
    use Logger\StderrLoggerTrait;

    public const EXIT_CODE_NO_ERRORS_FOUND = 0;

    /**
     * Runs PHPCS and then filters out unmodified lines.
     *
     * @return int exitCode
     */
    public function run(): int
    {
        $this->initLogger(
            $this->isDebugging() ? \Monolog\Logger::DEBUG : \Monolog\Logger::ERROR
        );

        $argv = [
            $_SERVER['argv'][0],
            '--no-colors',
            '--report=json',
        ];
        // inherit arguments
        foreach (array_slice($_SERVER['argv'], 1) as $v) {
            if (!$this->matchesIgnoringArgs($v)) {
                $argv[] = $v;
            } else {
                $this->logger->debug('Ignored', ['arg' => $v]);
            }
        }

        // Parsing git-diff text
        $parser = new DiffParser();
        $diffs = $parser->parseDiffOnlyAddedLines($this->readFromStdin());

        if (sizeof($diffs) === 0) {
            $this->logger->info('No added lines found');
            return self::EXIT_CODE_NO_ERRORS_FOUND;
        }
        // Appending modified file names to argv
        foreach (array_keys($diffs) as $filename) {
            $argv[] = $filename;
        }

        $this->logger->debug('Runtime Arguments', ['argv' => $argv]);

        // Override PHPCS runtime arguments
        $_SERVER['argv'] = $argv;

        ob_start();
        $runner = new \PHP_CodeSniffer\Runner();
        $exitCode = $runner->runPHPCS();
        $result = ob_get_clean();

        $this->logger->debug('Result', ['exitCode' => $exitCode]);

        // Note: PHPCS exit code
        // 0: No errors found.
        // 1: Errors found, but none of them can be fixed by PHPCBF.
        // 2: Errors found, and some can be fixed by PHPCBF.
        if ($exitCode !== 0 && $exitCode !== 1 && $exitCode !== 2) {
            echo $result;
            // Returns original exit code.
            return $exitCode;
        }

        $report = json_decode($result, true);
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

            $filter = new PhpcsReportFilter();
            $filtered = $filter->filterOutUnmodifiedLines($report, $diffs);

            echo json_encode($filtered);

            $exitCode = $this->getExitCode($filtered);
            $this->logger->debug('Filtered Result', ['exitCode' => $exitCode]);
        }

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
     * Gets exit code by report.
     *
     * @param array $report
     * @return int
     */
    protected function getExitCode(array $report): int
    {
        if (($report['totals']['errors'] ?? 0) === 0 && ($report['totals']['warnings'] ?? 0) === 0) {
            // 0: No errors found.
            return 0;
        } elseif (($report['totals']['fixable'] ?? 0) === 0) {
            // 1: Errors found, but none of them can be fixed by PHPCBF.
            return 1;
        } else {
            // 2: Errors found, and some can be fixed by PHPCBF.
            return 2;
        }
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
        return strlen($text) >= strlen($part) && strpos($text, $part) === 0;
    }

    /**
     * Reads string from stdin.
     *
     * @return string
     */
    private function readFromStdin(): string
    {
        return file_get_contents('php://stdin');
    }

    /**
     * Wether it is debugging mode.
     *
     * @return bool
     */
    private function isDebugging(): bool
    {
        $debugging = getenv('DEBUGGING');
        return !empty($debugging) && $debugging;
    }
}
