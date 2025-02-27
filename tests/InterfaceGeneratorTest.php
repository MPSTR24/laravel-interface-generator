<?php

namespace Mpstr24\InterfaceTyper\Tests;

use Illuminate\Support\Facades\Artisan;
use Mpstr24\InterfaceTyper\Tests\Helpers\TestUtilities;

/**
 * @covers \Mpstr24\InterfaceTyper\Console\Commands\InterfaceGenerator::getInterfaceFromFillables
 */
// #[CoversMethod('Mpstr24\Interfacetyper\Console\Commands\InterfaceGenerator','getInterfaceFromFillables')] FIXME
class InterfaceGeneratorTest extends TestCase
{

    /**
     * @return void
     */
    public function test_generates_correct_test_user_interface_from_fillables(): void
    {
        Artisan::call('generate:interfaces', ['--mode' => 'fillables']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            "export interface TestUserInterface {",
            "first_name: any;",
            "last_name: any;",
            "email: any;",
            "}"
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }
}
