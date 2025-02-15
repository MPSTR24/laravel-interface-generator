<?php

namespace Mpstr24\InterfaceTyper\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        foreach ($models as $model) {
            $this->getInterfaceFromFillables($model);
        }

        // getting the model's columns will provide datatypes instead + id or uuid + created_at/updated_at
        // if $table->timestamps() is present

        foreach ($models as $model) {
            // get the current table
            $table = $model->getTable();
            $this->info($table);

            // this provides a complete type list compared to fillables
            $columns = DB::connection()->getSchemaBuilder()->getColumns($table);

            $model_interface = "export interface " . class_basename($model) . " { \n";

            // parse returned columns
            foreach ($columns as $column) {
                $column_name = $column['name'];
                $type = $this->mapTypes($column['type_name']);
                $column_nullable = $column['nullable'];

                if ($column_nullable){
                    $model_interface .= '   ' . $column_name . '?: ' . $type . ";\n";
                }else {
                    $model_interface .= '   ' . $column_name . ': ' . $type . ";\n";
                }

            }
            $model_interface .= "}\n";

            $this->info($model_interface);

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

    private function getInterfaceFromFillables(Model $model): void
    {
        // now create rough interfaces

        $model_interface = "export interface " . class_basename($model) . " { \n";
        foreach ($model->getFillable() as $fillable) {
            $model_interface .= '   ' . $fillable . ": any;\n";
        }
        $model_interface .= "}";

        $this->info($model_interface);
        // interface is missing id, created_at, updated_at
    }

    private function mapTypes($column_type_name): string
    {
        return match ($column_type_name) {
            'tinyint' => 'boolean',
            'char', 'string', 'text', 'varchar', 'tinytext', 'mediumtext', 'longtext', 'time', 'json' => 'string',
            'smallint',  'mediumint', 'int', 'bigint', 'float', 'decimal', 'double', 'year' => 'number',
            'datetime', 'date', 'timestamp' => 'date',
            'blob' => 'unknown', // TODO conduct testing to narrow down type
            'geometry' => 'unknown', // TODO conduct testing to narrow down type
            'enum' => 'unknown', // TODO conduct testing to narrow down type
            'set' => 'unknown',// TODO conduct testing to narrow down type
            // if not matched return "unknown" type, update this as unknowns are found
            default => 'unknown'
        };
    }
}
