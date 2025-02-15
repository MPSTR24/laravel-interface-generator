<?php

namespace Mpstr24\InterfaceTyper\Console\Commands;

use Illuminate\Console\Command;
use ReflectionClass;
use ReflectionException;

class InterfaceGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:interfaces';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will automatically generate interfaces for your Laravel models based off their migrations';

    /**
     * Execute the console command.
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $models = $this->getModels();

        // with each model, now collect the fillables
        foreach ($models as $model) {
            $fillables = $model->getFillable();

            // now create rough interfaces

            $model_interface = "export interface " . class_basename($model) . " { \n";
            foreach ($fillables as $fillable) {
                $model_interface .= '   ' . $fillable . ": any;\n";
            }
            $model_interface .= "}";

            $this->info($model_interface);
            // interface is missing id, created_at, updated_at
        }

    }

    /**
     * @throws ReflectionException
     */
    private function getModels(): array
    {
        // Find all model within the project

        $models_path = app_path('Models');

        // remove . .. from results
        $files = array_diff(scandir($models_path), array('.', '..'));

        $models = [];

        foreach ($files as $file) {
            $this->info($file);

            // get file name only, strip .php
            $file_name_only = pathinfo($file, PATHINFO_FILENAME);

            // build model path
            $model_path = 'App\\Models\\' . $file_name_only;

            $model_reflection = new ReflectionClass($model_path);

            $models[] = $model_reflection->newInstance();
        }

        return $models;
    }
}
