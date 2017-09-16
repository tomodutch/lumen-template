<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\View\Factory;

class CMakeResource extends Command
{
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
        $this->createController();
    }

    public function createController()
    {
        $name = $this->argument('name');
        $camelCase = camel_case($name);
        $pascalCase = ucfirst($camelCase);
        $className = implode('', [$pascalCase, 'Controller']);
        $modelName = $pascalCase . '';
        $plural = $this->argument('plural');

        $dataTypes = collect($this->argument('fields'))->map(function ($definition) {
            return DataType::fromString($definition);
        });

        /** @var Factory $view */
        $view = app('view');
        $view->addLocation(implode(DIRECTORY_SEPARATOR, [__DIR__, 'stubs']));

        $viewParams = compact(
            'pascalCase',
            'className',
            'modelName',
            'plural',
            'camelCase',
            'dataTypes');

        $controllerContents = $view->make('controller', $viewParams)->render();
        $dest = base_path(implode(DIRECTORY_SEPARATOR, ['app', 'Http', 'Controllers', "$className.php"]));
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
                $this->line("<info>Overriding controller:</info> {$dest}");

                return;
            }
        }

        $fh = fopen($dest, 'w');
        fwrite($fh, "<?php" . PHP_EOL . PHP_EOL . $controllerContents);
        fclose($fh);

        $this->line("<info>Created controller:</info> {$dest}");
    }
}
