<?php

declare(strict_types=1);

namespace PhpcsAddedLines;

use PHPUnit\Framework\TestCase;

class DiffParserTest extends TestCase
{
    private $parser;

    public function setUp(): void
    {
        $this->parser = new DiffParser();
    }

    public function testParseDiffOnlyAddedLines1(): void
    {
        $diff = <<<END_OF_DIFF
diff --git a/README.md b/README.md
new file mode 100644
index 0000000..039df44
--- /dev/null
+++ b/README.md
@@ -0,0 +1,3 @@
+# phpcs-added-lines
+
+This is `phpcs` wrapper for filtering report based on diff text.

END_OF_DIFF;

        $this->assertSame(
            [
                'README.md' => [
                    1 => '# phpcs-added-lines',
                    2 => '',
                    3 => 'This is `phpcs` wrapper for filtering report based on diff text.',
                ]
            ],
            $this->parser->parseDiffOnlyAddedLines($diff)
        );
    }

    public function testParseDiffOnlyAddedLines2(): void
    {
        $diff = <<<END_OF_DIFF
diff --git a/README.md b/README.md
index 039df44..0d485ee 100644
--- a/README.md
+++ b/README.md
@@ -4,3 +4,9 @@ This is `phpcs` wrapper for filtering report based on diff text.

 - Filters by added/modified line ranges.
 - JSON reporting only.
+
+## Installation
+
+```
+$ composer require sameyasu/phpcs-added-lines
+```

END_OF_DIFF;

        $this->assertSame(
            [
                'README.md' => [
                    7 => '',
                    8 => '## Installation',
                    9 => '',
                    10 => '```',
                    11 => '$ composer require sameyasu/phpcs-added-lines',
                    12 => '```',
                ]
            ],
            $this->parser->parseDiffOnlyAddedLines($diff)
        );
    }

    public function testParseDiffOnlyAddedLines3(): void
    {
        $diff = <<<END_OF_DIFF
diff --git a/composer.json b/composer.json
index 960de3c..175461d 100644
--- a/composer.json
+++ b/composer.json
@@ -11,7 +11,10 @@
     "license": "BSD-3-Clause",
     "autoload": {
         "psr-4": {
-            "PhpcsAddedLines\\": "src/"
+            "PhpcsAddedLines\\": [
+                "src/",
+                "tests/"
+            ]
         }
     },
     "config": {
@@ -21,5 +24,8 @@
     },
     "bin": [
         "bin/phpcs-added-lines"
-    ]
+    ],
+    "require-dev": {
+        "phpunit/phpunit": "^8.5"
+    }
 }

END_OF_DIFF;

        $this->assertSame(
            [
                'composer.json' => [
                    14 => '            "PhpcsAddedLines\\": [',
                    15 => '                "src/",',
                    16 => '                "tests/"',
                    17 => '            ]',
                    27 => '    ],',
                    28 => '    "require-dev": {',
                    29 => '        "phpunit/phpunit": "^8.5"',
                    30 => '    }',
                ]
            ],
            $this->parser->parseDiffOnlyAddedLines($diff)
        );
    }
}
