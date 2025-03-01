<?php

namespace Mpstr24\InterfaceTyper\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;

class InterfaceGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:interfaces
        {--M|mode=migrations : Mode to generate interfaces (migrations|fillables)}
        {--S|suffix=Interface : Add a suffix to generated interface names}
        {--model=all : Select the model to generate an interface for, default is all models}
        {--R|relationships=true : Enable following relationships (True|False)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will automatically generate interfaces for your Laravel models based off their migrations';

    /**
     * Execute the console command.
     *
     * @throws ReflectionException
     */
    public function handle(): int
    {
        $mode = $this->option('mode');
        if (! in_array($mode, ['migrations', 'fillables'], true)) {
            $this->error('Invalid generation mode, please use either "migrations" or "fillables".');

            return self::FAILURE;
        }

        $suffix = $this->normaliseUserInput($this->option('suffix'));

        $model_selection = $this->normaliseUserInput($this->option('model'));

        $relationships = filter_var($this->option('relationships'), FILTER_VALIDATE_BOOLEAN);

        $models = $this->getModels($model_selection);

        if ($mode === 'migrations') {

            // check if the user has migrations table, if they haven't this command will do nothing so alert the user
            if (! Schema::hasTable('migrations')) {
                $this->error('You have not ran any migrations, please run them first or use --mode=fillables');

                return self::FAILURE;
            }

            foreach ($models as $model) {

                $this->getInterfaceFromMigrations(
                    model: $model,
                    suffix: $suffix,
                    relationships: $relationships
                );
            }

        } else {

            foreach ($models as $model) {
                $this->getInterfaceFromFillables(
                    model: $model,
                    suffix: $suffix,
                    relationships: $relationships
                );
            }

        }

        return self::SUCCESS;
    }

    /**
     * @return array<Model>
     *
     * @throws ReflectionException
     */
    private function getModels(?string $model_selection): array
    {
        // Find all model within the project
        $models_path = app_path('Models');

        // remove . .. from results
        $files = array_diff(scandir($models_path), ['.', '..']);

        $models = [];

        foreach ($files as $file) {

            // ensure php files only

            if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }

            // get file name only, strip .php
            $file_name_only = pathinfo($file, PATHINFO_FILENAME);

            if (! empty($model_selection) && strtolower($model_selection) !== 'all' && strtolower($model_selection) !== strtolower($file_name_only)) {
                continue;
            }

            // build model path
            $model_path = 'App\\Models\\'.$file_name_only;

            // check class exists
            if (! class_exists($model_path)) {
                continue;
            }

            $model_reflection = new ReflectionClass($model_path);

            $model_instance = $model_reflection->newInstance();

            // ensure it is a model instance
            if (! $model_instance instanceof Model) {
                continue;
            }

            $models[] = $model_instance;
        }

        return $models;
    }

    /**
     * @throws ReflectionException
     */
    private function getInterfaceFromFillables(Model $model, ?string $suffix, bool $relationships): void
    {
        $interface_name = class_basename($model);
        if (! empty($suffix)) {
            $interface_name .= $suffix;
        }

        $model_interface = 'export interface '.$interface_name." { \n";
        foreach ($model->getFillable() as $fillable) {
            $model_interface .= "   $fillable: any;\n";
        }

        if ($relationships) {
            $this->addRelationshipsToInterface($model, $suffix, $model_interface);
        }

        $model_interface .= "}\n";

        $this->info($model_interface);
    }

    /**
     * @throws ReflectionException
     */
    private function getInterfaceFromMigrations(Model $model, ?string $suffix, bool $relationships): void
    {
        // get the current table
        $table = $model->getTable();

        // this provides a complete type list compared to fillables

        // account for sqlite not having getColumns
        // TODO implement tests for other DB types - mysql, sqlite for now
        if (DB::connection()->getDriverName() === 'sqlite') {
            $columns = DB::select("PRAGMA table_info({$table})");

            $columns = array_map(function ($column) {
                return [
                    'name' => $column->name,
                    'type_name' => $column->type,
                    'nullable' => ! $column->notnull,
                ];
            }, $columns);

        } else {
            // MySQL
            $columns = DB::connection()->getSchemaBuilder()->getColumns($table);
        }

        $interface_name = class_basename($model);
        if (! empty($suffix)) {
            $interface_name .= $suffix;
        }

        $model_interface = 'export interface '.$interface_name." { \n";

        // parse returned columns
        foreach ($columns as $column) {
            $column_name = $column['name'];
            $type = $this->mapTypes($column['type_name']);
            $column_nullable = ! empty($column['nullable']) ? '?' : '';

            $model_interface .= "   $column_name$column_nullable: $type;\n";

        }

        if ($relationships) {
            $this->addRelationshipsToInterface($model, $suffix, $model_interface);
        }

        $model_interface .= "}\n";

        $this->info($model_interface);
    }

    private function mapTypes(string $column_type_name): string
    {
        // TODO maybe map per DB Driver?
        return match ($column_type_name) {
            'tinyint' => 'boolean',
            'TEXT', 'char', 'string', 'text', 'varchar', 'tinytext', 'mediumtext', 'longtext', 'time', 'json' => 'string',
            'smallint',  'mediumint', 'int', 'integer', 'INTEGER', 'bigint', 'float', 'decimal', 'double', 'year' => 'number',
            'datetime', 'date', 'timestamp' => 'Date',
            'blob' => 'unknown', // TODO conduct testing to narrow down type
            'geometry' => 'unknown', // TODO conduct testing to narrow down type
            'enum' => 'unknown', // TODO conduct testing to narrow down type
            'set' => 'unknown',// TODO conduct testing to narrow down type
            // if not matched return "unknown" type, update this as unknowns are found
            default => 'unknown' // Set to $column_type_name to help debug unknown types when they appear
        };
    }

    /**
     * @return array<string, Relation<Model, Model, mixed>>
     */
    private function getRelationshipsFromMethods(Model $model): array
    {
        $relationships = [];
        // relationships within models are going to be public functions whose names match other models
        // use reflection to access these
        $reflection = new ReflectionClass($model);
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {

            // relationship methods will have no parameters
            if ($method->getNumberOfParameters() > 0) {
                continue;
            }

            // only get methods on the actual user's model, not the base class
            if ($method->getDeclaringClass()->getName() !== get_class($model)) {
                continue;
            }

            // check the method works on the model, as we may have made a plural method singular
            try {
                $relationship = $method->invoke($model);

                if ($relationship instanceof Relation) {
                    $relationships[$method->getName()] = $relationship;
                }

            } catch (\Exception $e) {
                continue;
            }

        }

        return $relationships;
    }

    /**
     * @throws ReflectionException
     */
    private function addRelationshipsToInterface(Model $model, ?string $suffix, string &$model_interface): void
    {
        $model_relationships = $this->getRelationshipsFromMethods($model);

        foreach ($model_relationships as $method_name => $relationship) {

            // check that the method exists in the model
            if (! method_exists($model, $method_name)) {
                continue;
            }

            // check that getRelated() can run on the method
            try {
                $related_method_instance = $model->{$method_name}();

                if (! method_exists($related_method_instance, 'getRelated')) {
                    continue;
                }

                $related_model = $model->{$method_name}()->getRelated();
            } catch (Throwable $e) {
                continue;
            }

            // get the related model
            $related_interface_name = class_basename($related_model);
            if (! empty($suffix)) {
                $related_interface_name .= $suffix;
            }

            // polymorphic relationships
            if ($relationship instanceof MorphTo) {
                // perform a look up of models with the method relating to the relationship
                $found_models = $this->findModelsContainingPolymorphicRelationship(class_basename($model));

                $union_types = [];
                foreach ($found_models as $found_model) {
                    $model_name = class_basename($found_model);
                    if (! empty($suffix)) {
                        $model_name .= $suffix;
                    }
                    $union_types[] = $model_name;
                }
                $union_types = implode(' | ', array_unique($union_types));
                $model_interface .= "   $method_name?: $union_types;\n";

            } elseif ($relationship instanceof HasMany || $relationship instanceof BelongsToMany || $relationship instanceof MorphMany) {
                $model_interface .= "   $method_name?: {$related_interface_name}[];\n";
            } else {
                $model_interface .= "   $method_name?: $related_interface_name;\n";
            }
        }
    }

    /**
     * @return array<Model>
     *
     * @throws ReflectionException
     */
    public function findModelsContainingPolymorphicRelationship(string $polymorphic_model_name)
    {
        $models = $this->getModels('all');

        $found_models = [];

        foreach ($models as $model) {
            $reflection = new ReflectionClass($model);
            // Check if the method exists on the model
            if ($reflection->hasMethod(Str::plural($polymorphic_model_name))) {
                $found_models[] = $model;
            }
        }

        return $found_models;
    }

    /**
     * @param  array<mixed>|bool|string|null  $input
     */
    private function normaliseUserInput(array|bool|string|null $input): ?string
    {
        if (is_array($input)) {
            $input = $input[0] ?? null;
        }

        if (is_bool($input)) {
            $input = null;
        }

        return is_string($input) ? $input : null;
    }
}
