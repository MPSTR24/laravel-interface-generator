<?php

namespace Mpstr24\InterfaceTyper\Tests;

use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * @covers \Mpstr24\InterfaceTyper\Console\Commands\InterfaceGenerator::getInterfaceFromFillables
 */
// #[CoversMethod('Mpstr24\Interfacetyper\Console\Commands\InterfaceGenerator','getInterfaceFromFillables')] FIXME
class InterfaceGeneratorTest extends TestCase
{
    public function test_generates_correct_test_user_interface_from_fillables()
    {
        $this->artisan('generate:interfaces', ['--mode' => 'fillables'])
            ->assertExitCode(0)
            ->expectsOutputToContain("export interface TestUserInterface { \n   first_name: any;\n   last_name: any;\n   email: any;\n}\n");
    }
}
