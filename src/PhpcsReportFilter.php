<?php

namespace PhpcsAddedLines;

use SebastianBergmann\Diff\Parser;
use SebastianBergmann\Diff\Line;

/**
 * Filters for phpcs report.
 */
class PhpcsReportFilter
{
    /**
     * Filters out unmodified lines from phpcs json report.
     *
     * @param array $phpcsReport
     * @param array $diffArray
     * @return array
     */
    public function filterOutUnmodifiedLines(array $phpcsReport, array $diffArray): array
    {
        if (isset($phpcsReport['files'])) {
            $filtered = $phpcsReport['files'];
            $totalErrors = 0;
            $totalWarnings = 0;
            $totalFixable = 0;

            foreach ($phpcsReport['files'] as $filename => $report) {
                if (isset($diffArray[$filename])) {
                    $diff = $diffArray[$filename];
                    if ($report['errors'] > 0 || $report['warnings'] > 0) {
                        $errors = 0;
                        $warnings = 0;
                        $messages = [];

                        foreach ($report['messages'] as $msg) {
                            if (isset($diff[$msg['line']])) {
                                // it's matched with added line
                                if ($msg['type'] === 'ERROR') {
                                    $errors++;
                                    $totalErrors++;
                                    $messages[] = $msg;
                                } elseif ($msg['type'] === 'WARNING') {
                                    $warnings++;
                                    $totalWarnings++;
                                    $messages[] = $msg;
                                }

                                if ($msg['fixable'] === true) {
                                    $totalFixable++;
                                }
                            }
                        }

                        $filtered[$filename]['errors'] = $errors;
                        $filtered[$filename]['warnings'] = $warnings;
                        $filtered[$filename]['messages'] = $messages;
                    }
                } else {
                    unset($filtered[$filename]);
                }
            }

            $filteredIssues = [
                'totals' => [
                    'errors' => $totalErrors,
                    'warnings' => $totalWarnings,
                    'fixable' => $totalFixable,
                ],
                'files' => $filtered
            ];

            return $filteredIssues;
        } else {
            // nothing to do
            return $phpcsReport;
        }
    }
}
