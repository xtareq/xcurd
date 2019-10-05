<?php

namespace Xcurd\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Facades\Storage;
class Xcurd extends GeneratorCommand
{
        /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:curd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Curd class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Curd';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */

    protected function getStub()
    {
        $stub = null;

        if ($this->option('curd')) {
            $stub = '/stubs/controller.curd.stub';
        }

        $stub = $stub ?? '/stubs/controller.plain.stub';

        return __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $controllerNamespace = $this->getNamespace($name);

        $replace = [];
        if($this->option('curd')){
            $replace = $this->buildCurdReplacements($replace);
            $model = $this->option('curd');
            $data = "\nRoute::resource('";
            $data .= lcfirst($model);
            $data .= "',";
            $data .="'";
            $data .=  Str::after($name, 'App\Http\Controllers\\');
            $data .= "');";
            file_put_contents(base_path().'\routes\web.php',$data, FILE_APPEND);
        }

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }


    /**
     * Build the curd replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildCurdReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('curd'));



        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }



       if($this->option('view')){
            $rootView = $this->option('view');
            $path = resource_path().'\views\\'.$rootView.'\\'.lcfirst(class_basename($modelClass));
            if(!file_exists($path)){
                mkdir($path,0777,true);
                //Storage::makeDirectory($path);
            }


            if(file_exists($path)){

                shell_exec('cp -r  '.__DIR__.'/curd-stubs/*  '.$path.'\\');
            }


        }else{
            if(!file_exists(resource_path().'\views\\'.lcfirst(class_basename($modelClass)))){
                mkdir(resource_path().'\views\\'.lcfirst(class_basename($modelClass)),0777);
            }


            if(file_exists(resource_path().'\views\\'.lcfirst(class_basename($modelClass)))){

                shell_exec('cp -r  '.__DIR__.'/curd-stubs/*  '.resource_path().'\views\\'.lcfirst(class_basename($modelClass)).'\\');
            }

        }


        if($this->option('view')){
            $viewPath = $this->option('view').".".lcfirst(class_basename($modelClass));
        }else{
            $viewPath = lcfirst(class_basename($modelClass));
        }

       

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            'DummyModelStringPlural' => Str::plural(lcfirst(class_basename($modelClass))),
            'ModelInLower' => strtolower(class_basename($modelClass)),
            'DummyModelString' => lcfirst(class_basename($modelClass)),
            'ViewPath' => $viewPath
        ]);

    }



    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $model = trim(str_replace('/', '\\', $model), '\\');

        if (! Str::startsWith($model, $rootNamespace = $this->laravel->getNamespace())) {
            $model = $rootNamespace.$model;
        }

        return $model;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['curd', 'c', InputOption::VALUE_OPTIONAL, 'Generate a new curd class.'],
            ['view', 'x', InputOption::VALUE_OPTIONAL, 'Generate a new curd class.'],
        ];
    }
}
