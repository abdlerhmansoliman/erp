<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeRepository extends Command
{
    /**
     * اسم الكوماند في التيرمنال
     */
    protected $signature = 'make:repository {name}';

    /**
     * وصف الكوماند
     */
    protected $description = 'Create a new Repository class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');

        // مسار الريبو
        $path = app_path("Repositories/{$name}.php");

        // لو الملف موجود بالفعل
        if ($this->files->exists($path)) {
            $this->error("Repository already exists!");
            return;
        }

        // تأكد إن المجلد موجود
        $this->makeDirectory($path);

        // اكتب الكود في الملف
        $stub = $this->getStub($name);
        $this->files->put($path, $stub);

        $this->info("Repository created: {$path}");
    }

    protected function getStub($name)
    {
        return <<<PHP
<?php

namespace App\Repositories;

class {$name}
{
    public function all()
    {
        // TODO: implement all()
    }

    public function find(\$id)
    {
        // TODO: implement find()
    }

    public function create(array \$data)
    {
        // TODO: implement create()
    }

    public function update(\$id, array \$data)
    {
        // TODO: implement update()
    }

    public function delete(\$id)
    {
        // TODO: implement delete()
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
