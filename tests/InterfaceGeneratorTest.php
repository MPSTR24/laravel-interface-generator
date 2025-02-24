<?php

it('generates the correct "TestUser" interface from fillables', function () {
    $this->artisan('generate:interfaces', ['--mode' => 'fillables'])
        ->assertExitCode(0)
        ->expectsOutputToContain("export interface TestUserInterface { \n   first_name: any;\n   last_name: any;\n   email: any;\n}\n");
});
