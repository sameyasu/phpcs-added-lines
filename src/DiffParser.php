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
                foreach ($chunk->getLines() as $num => $line) {
                    if ($line->getType() === Line::ADDED) {
                        $lineNum = ($chunk->getStart() === 0) ? $num + $chunk->getEnd() : $num + $chunk->getEnd() - 1;
                        $lineNums[$lineNum]['content'] = $line->getContent();
                        $lineNums[$lineNum]['position'] = $num + 1;
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
