<?php

namespace Mpstr24\InterfaceTyper\Tests\Helpers;

use Mpstr24\InterfaceTyper\Tests\TestCase;

class TestUtilities
{

    /**
     * @param string $interfaceOutput
     * @return array
     */
    public static function interfaceOutputNormaliser(string $interfaceOutput): array
    {
        return array_filter(array_map('trim', explode("\n", $interfaceOutput)));
    }

    /**
     * @param array $expectedOutput
     * @param array $actualOutput
     * @param TestCase $testCase
     * @return void
     */
    public static function interfaceLineCountMatcher(array $expectedOutput, array $actualOutput, TestCase $testCase): void
    {
        $testCase->assertCount(count($expectedOutput), $actualOutput);
    }

    /**
     * @param array $expectedOutput
     * @param array $actualOutput
     * @param TestCase $testCase
     * @return void
     */
    public static function interfaceLineMatcher(array $expectedOutput, array $actualOutput, TestCase $testCase): void
    {
        foreach ($expectedOutput as $index => $expectedLine) {
            $testCase->assertSame($expectedLine, $actualOutput[$index], "Line $index does not match the expected. Expected $expectedLine but received $actualOutput[$index] instead.");
        }
    }
}
