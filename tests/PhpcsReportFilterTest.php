<?php

declare(strict_types=1);

namespace PhpcsAddedLines;

use PHPUnit\Framework\TestCase;

class PhpcsReportFilterTest extends TestCase
{
    private $filter;

    public function setUp(): void
    {
        $this->filter = new PhpcsReportFilter();
    }

    public function testFilterOutUnmodifiedLines1(): void
    {
        $expected = [
            'totals' => [
                'errors' => 0,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => []
        ];

        $report = [
            'totals' => [
                'errors' => 0,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 0,
                    'warnings' => 0,
                    'messages' => []
                ]
            ]
        ];

        $diff = [];

        $actual = $this->filter->filterOutUnmodifiedLines($report, $diff);

        $this->assertSame($expected, $actual);
    }

    public function testFilterOutUnmodifiedLines2(): void
    {
        $expected = [
            'totals' => [
                'errors' => 0,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 0,
                    'warnings' => 0,
                    'messages' => []
                ]
            ]
        ];

        $report = [
            'totals' => [
                'errors' => 0,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 0,
                    'warnings' => 0,
                    'messages' => []
                ]
            ]
        ];

        $diff = [
            'test.php' => [
                1 => '<?php',
            ]
        ];

        $actual = $this->filter->filterOutUnmodifiedLines($report, $diff);

        $this->assertSame($expected, $actual);
    }

    public function testFilterOutUnmodifiedLines3(): void
    {
        $expected = [
            'totals' => [
                'errors' => 1,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 1,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'This is a test',
                            'line' => 2,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ]
                    ]
                ]
            ]
        ];

        $report = [
            'totals' => [
                'errors' => 1,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 1,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'This is a test',
                            'line' => 2,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ]
                    ]
                ]
            ]
        ];

        $diff = [
            'test.php' => [
                1 => '<?php',
                2 => '',
            ]
        ];

        $actual = $this->filter->filterOutUnmodifiedLines($report, $diff);

        $this->assertSame($expected, $actual);
    }

    public function testFilterOutUnmodifiedLines4(): void
    {
        $expected = [
            'totals' => [
                'errors' => 1,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 1,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'This is a test',
                            'line' => 2,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ]
                    ]
                ]
            ]
        ];

        $report = [
            'totals' => [
                'errors' => 2,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 2,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'This is a test',
                            'line' => 2,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ],
                        [
                            'message' => 'out of the diff ranges',
                            'line' => 3,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ]
                    ]
                ]
            ]
        ];

        $diff = [
            'test.php' => [
                1 => '<?php',
                2 => '',
            ]
        ];

        $actual = $this->filter->filterOutUnmodifiedLines($report, $diff);

        $this->assertSame($expected, $actual);
    }

    public function testFilterOutUnmodifiedLines5(): void
    {
        $expected = [
            'totals' => [
                'errors' => 1,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 1,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'This is a test',
                            'line' => 2,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ]
                    ]
                ]
            ]
        ];

        $report = [
            'totals' => [
                'errors' => 1,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [
                'test.php' => [
                    'errors' => 1,
                    'warnings' => 0,
                    'messages' => [
                        [
                            'message' => 'This is a test',
                            'line' => 2,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ],
                        [
                            'message' => 'out of the diff ranges',
                            'line' => 3,
                            'type' => 'ERROR',
                            'fixable' => false,
                        ]
                    ]
                ]
            ]
        ];

        $diff = [
            'test.php' => [
                1 => '<?php',
                2 => '',
            ]
        ];

        $actual = $this->filter->filterOutUnmodifiedLines($report, $diff);

        $this->assertSame($expected, $actual);
    }
}
