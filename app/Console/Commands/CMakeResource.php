<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\View\Factory;

class CMakeResource extends Command
{
    /** @var Factory $view */
    private $view;

    /** @var string */
    private $pascalCase = '';

    /** @var string */
    private $camelCase = '';

    /** @var string */
    private $plural = '';

    /** @var array */
    private $dataTypes = [];

    /** @var array */
    private $primaryIdDataTypes = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cmake:resource {name} {plural} {fields*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate an API resource including its model, resource, controller, factory and migrations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->camelCase = camel_case($name);
        $this->pascalCase = ucfirst($this->camelCase);
        $this->plural = $this->argument('plural');

        $this->view = app('view');
        $this->view->addLocation(implode(DIRECTORY_SEPARATOR, [__DIR__, 'stubs']));

        $this->dataTypes = collect($this->argument('fields'))->map(function ($definition) {
            return DataType::fromString($definition);
        });

        $this->primaryIdDataTypes = $this->dataTypes->filter(function (DataType $dataType) {
            return $dataType->isPrimaryKey();
        })->toArray();

        $this->createModel();
        $this->createViewResource();
        $this->createCollectionViewResource();
        $this->createController();
    }

    public function createViewResource()
    {
        $viewParams = $this->getViewParams();
        $modelContents = $this->view->make('resource', $viewParams)->render();
        $dest = base_path(implode(DIRECTORY_SEPARATOR,
            ['app', 'Resources', "{$this->pascalCase}.php"]));

        $this->writeFile($dest, '<?php' . PHP_EOL . PHP_EOL . $modelContents);
    }

    public function createCollectionViewResource()
    {
        $modelContents = $this->view->make('resources', $this->getViewParams())->render();
        $dest = base_path(implode(DIRECTORY_SEPARATOR,
            ['app', 'Resources', "{$this->pascalCase}Collection.php"]));

        $this->writeFile($dest, '<?php' . PHP_EOL . PHP_EOL . $modelContents);
    }

    public function createModel()
    {
        $viewParams = $this->getViewParams();

        $modelContents = $this->view->make('model', $viewParams)->render();
        $dest = base_path(implode(DIRECTORY_SEPARATOR,
            ['app', "{$this->pascalCase}.php"]));

        $this->writeFile($dest, '<?php' . PHP_EOL . PHP_EOL . $modelContents);
    }

    public function createController()
    {
        $viewParams = $this->getViewParams();
        $contents = $this->view->make('controller', $viewParams)->render();
        $dest = base_path(implode(DIRECTORY_SEPARATOR,
            ['app', 'Http', 'Controllers', "{$this->pascalCase}Controller.php"]));

        $this->writeFile($dest, '<?php' . PHP_EOL . PHP_EOL . $contents);
    }

    public function getViewParams()
    {
        return [
            'pascalCase' => $this->pascalCase,
            'camelCase' => $this->camelCase,
            'plural' => $this->plural,
            'dataTypes' => $this->dataTypes,
            'primaryIdDataTypes' => $this->primaryIdDataTypes
        ];
    }

    private function writeFile($dest, $contents)
    {
        if (file_exists($dest)) {
            $question = "File $dest already exists. Should I override this file? (Y/N)";
            $shouldOverride = null;
            while ($shouldOverride === null) {
                $answer = strtolower($this->ask($question));
                if (in_array($answer, ['y', 'n']) === false) {
                    $this->error('Answer should be either Y or N');
                } else {
                    $shouldOverride = $answer === 'y';
                }
            }

            if ($shouldOverride === false) {
                return;
            }
        }

        $fh = fopen($dest, 'w');
        fwrite($fh, $contents);
        fclose($fh);

        $this->line("<info>Created:</info> {$dest}");
    }
}
