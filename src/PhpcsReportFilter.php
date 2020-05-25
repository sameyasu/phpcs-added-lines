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
            $total_errors = 0;
            $total_warnings = 0;
            $total_fixable = 0;

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
                                    $total_errors++;
                                    $messages[] = $msg;
                                } elseif ($msg['type'] === 'WARNING') {
                                    $warnings++;
                                    $total_warnings++;
                                    $messages[] = $msg;
                                }

                                if ($msg['fixable'] === true) {
                                    $total_fixable++;
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
                    'errors' => $total_errors,
                    'warnings' => $total_warnings,
                    'fixable' => $total_fixable,
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
