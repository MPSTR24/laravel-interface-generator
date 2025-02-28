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
    public function test_generates_correct_test_user_interface_from_fillables(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'fillables', '--model' => 'TestUser', '--relationships' => 'False']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestUserInterface {',
            'first_name: any;',
            'last_name: any;',
            'email: any;',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_user_interface_from_migrations(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'migrations', '--model' => 'TestUser', '--relationships' => 'False']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestUserInterface {',
            'id: number;',
            'first_name: string;',
            'last_name: string;',
            'email: string;',
            'created_at?: date;',
            'updated_at?: date;',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_post_interface_from_fillables(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'fillables', '--model' => 'TestPost', '--relationships' => 'False']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestPostInterface {',
            'title: any;',
            'description: any;',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_post_interface_from_migrations(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'migrations', '--model' => 'TestPost', '--relationships' => 'False']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestPostInterface {',
            'id: number;',
            'title: string;',
            'description: string;',
            'user_id: number;',
            'created_at?: date;',
            'updated_at?: date;',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_user_interface_from_fillables_with_relationships(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'fillables', '--model' => 'TestUser', '--relationships' => 'True']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestUserInterface {',
            'first_name: any;',
            'last_name: any;',
            'email: any;',
            'testPosts?: TestPostInterface[];',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_user_interface_from_migrations_with_relationships(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'migrations', '--model' => 'TestUser', '--relationships' => 'True']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestUserInterface {',
            'id: number;',
            'first_name: string;',
            'last_name: string;',
            'email: string;',
            'created_at?: date;',
            'updated_at?: date;',
            'testPosts?: TestPostInterface[];',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_post_interface_from_fillables_with_relationships(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'fillables', '--model' => 'TestPost', '--relationships' => 'True']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestPostInterface {',
            'title: any;',
            'description: any;',
            'testUser?: TestUserInterface;',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }

    public function test_generates_correct_test_post_interface_from_migrations_with_relationships(): void
    {
        $this->withoutMockingConsoleOutput();
        Artisan::call('generate:interfaces', ['--mode' => 'migrations', '--model' => 'TestPost', '--relationships' => 'True']);
        $actualOutput = TestUtilities::interfaceOutputNormaliser(Artisan::output());

        $expectedOutput = [
            'export interface TestPostInterface {',
            'id: number;',
            'title: string;',
            'description: string;',
            'user_id: number;',
            'created_at?: date;',
            'updated_at?: date;',
            'testUser?: TestUserInterface;',
            '}',
        ];

        TestUtilities::interfaceLineCountMatcher($expectedOutput, $actualOutput, $this);

        TestUtilities::interfaceLineMatcher($expectedOutput, $actualOutput, $this);
    }
}
