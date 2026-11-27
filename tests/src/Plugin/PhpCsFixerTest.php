<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Plugin;

use PHPCensor\Builder;
use PHPCensor\Model\Build;
use PHPCensor\Model\BuildError;
use PHPCensor\Plugin\PhpCsFixer;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Unit test for the PHP CS Fixer plugin report parsing (sebastian/diff).
 */
class PhpCsFixerTest extends TestCase
{
    public function testProcessReportParsesDiff(): void
    {
        $diff = <<<'DIFF'
--- Original
+++ New
@@ -10,5 +10,5 @@
 class Foo
 {
-    public function bar() {
+    public function bar()
+    {
         return 1;
DIFF;

        $report = (string)\json_encode([
            'files' => [
                [
                    'name'          => 'src/Foo.php',
                    'appliedFixers' => ['braces_position'],
                    'diff'          => $diff,
                ],
            ],
        ]);

        $builder = $this->createStub(Builder::class);
        $build   = $this->createMock(Build::class);

        $reportedErrors = [];
        $build
            ->expects($this->exactly(2))
            ->method('reportError')
            ->willReturnCallback(function (...$arguments) use (&$reportedErrors): void {
                $reportedErrors[] = $arguments;
            });

        $reflection = new ReflectionClass(PhpCsFixer::class);
        $plugin     = $reflection->newInstanceWithoutConstructor();
        $reflection->getProperty('builder')->setValue($plugin, $builder);
        $reflection->getProperty('build')->setValue($plugin, $build);
        $reflection->getProperty('reportErrors')->setValue($plugin, true);

        $warnings = $reflection->getMethod('processReport')->invoke($plugin, $report);

        self::assertSame(1, $warnings);

        [, $plugin, $message, $severity, $file, $line] = $reportedErrors[0];
        self::assertSame('php_cs_fixer', $plugin);
        self::assertSame(BuildError::SEVERITY_LOW, $severity);
        self::assertSame('src/Foo.php', $file);
        self::assertSame(12, $line);
        self::assertStringContainsString("-    public function bar() {\r\n+    public function bar()\r\n+    {", $message);

        self::assertSame('PHP CS Fixer failed fixers: braces_position', $reportedErrors[1][2]);
    }
}
