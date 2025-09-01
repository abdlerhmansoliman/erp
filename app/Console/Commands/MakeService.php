<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{

    protected $signature = 'make:service {name}';

    protected $description = 'Create a new Service class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');

        $path = app_path("Services/{$name}.php");

        if ($this->files->exists($path)) {
            $this->error("Service already exists!");
            return;
        }

        $this->makeDirectory($path);

        $stub = $this->getStub($name);
        $this->files->put($path, $stub);

        $this->info("Service created: {$path}");
    }

    protected function getStub($name)
    {
        return <<<PHP
<?php

namespace App\Services;

class {$name}
{
    public function __construct()
    {
        // Inject dependencies if needed (e.g. repositories)
    }
}
PHP;
    }

    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }
    }
}
