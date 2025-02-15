<?php

namespace Mpstr24\InterfaceTyper\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ReflectionClass;
use ReflectionException;

class InterfaceGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:interfaces {--M|mode=migrations : Mode to generate interfaces (migrations|fillables)}';

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
    public function handle(): int
    {
        $mode = $this->option('mode');
        if (! in_array($mode, ['migrations', 'fillables'])) {
            $this->error('Invalid generation mode, please use either "migrations" or "fillables".');
            return self::FAILURE;
        }

        $models = $this->getModels();

        if ($this->option('mode') === 'migrations') {

            // check if the user has migrations table, if they haven't this command will do nothing so alert the user
            if (! Schema::hasTable('migrations')) {
                $this->error('You have not ran any migrations, please run them first or use --mode=fillables');
                return self::FAILURE;
            }

            foreach ($models as $model) {
                $this->getInterfaceFromMigrations($model);
            }

        }elseif ($this->option('mode') === 'fillables') {
            foreach ($models as $model) {
                $this->getInterfaceFromFillables($model);
            }
        }

        return self::SUCCESS;
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

    private function getInterfaceFromMigrations(Model $model): void
    {
        // get the current table
        $table = $model->getTable();

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
