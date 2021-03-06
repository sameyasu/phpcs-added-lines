<?php

namespace PhpcsAddedLines\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Log\LoggerAwareTrait;

/**
 * LoggerTrait to output stderr
 */
trait StderrLoggerTrait
{
    use LoggerAwareTrait;

    /**
     * Initialize $this->logger property.
     *
     * @param int $logLevel default: ERROR
     * @return void
     */
    protected function initLogger(int $logLevel = \Monolog\Logger::ERROR): void
    {
        $logger = new \Monolog\Logger(__CLASS__);

        $streamHandler = new StreamHandler('php://stderr', $logLevel);
        $formatter = new LineFormatter(
            "[%level_name%] %message% %context% %extra%\n", // logging format
            null, // use default
            true, // allowInlineLineBreaks: true
            true  // ignoreEmptyContextAndExtra: true
        );
        $streamHandler->setFormatter($formatter);

        $logger->setHandlers([
            $streamHandler
        ]);

        $this->setLogger($logger);
    }
}
