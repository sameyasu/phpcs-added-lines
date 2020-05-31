<?php

namespace PhpcsAddedLines;

use SebastianBergmann\Diff\Parser;
use SebastianBergmann\Diff\Line;

/**
 * Paser of diff formatting.
 */
class DiffParser
{
    /**
     * Parses & filters changed lines.
     *
     * @param string $diffText
     * @return array parsed json content
     */
    public function parseDiffOnlyAddedLines(string $diffText): array
    {
        $parser = new Parser();
        $diffs = $parser->parse($diffText);

        $map = [];

        foreach ($diffs as $diff) {
            $lineNums = [];
            foreach ($diff->getChunks() as $chunk) {
                $lineNum = 0;
                foreach ($chunk->getLines() as $num => $line) {
                    if (
                        $line->getType() === Line::UNCHANGED ||
                        $line->getType() === Line::ADDED
                    ) {
                        $lineNum = ($lineNum === 0) ? $num + $chunk->getEnd() : $lineNum + 1;
                        if ($line->getType() === Line::ADDED) {
                            $lineNums[$lineNum] = $line->getContent();
                        }
                    }
                }
            }

            if (sizeof($lineNums) > 0) {
                // trim unused prefix
                $filename = preg_replace('|\Ab/|', '', $diff->getTo());
                $map[$filename] = $lineNums;
            }
        }

        return $map;
    }
}
