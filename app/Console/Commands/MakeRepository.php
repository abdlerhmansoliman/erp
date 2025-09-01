<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use File;

class MakeRepository extends Command
{
    protected $signature = 'make:repository {name}';
    protected $description = 'Create a new interface and repository class';

    public function handle()
    {
        $name = $this->argument('name'); // e.g., User

        // Paths
        $interfaceDir = app_path('Repositories/Interfaces');
        $repoDir = app_path('Repositories');

        // Ensure directories exist
        if (!File::exists($interfaceDir)) {
            File::makeDirectory($interfaceDir, 0755, true);
        }
        if (!File::exists($repoDir)) {
            File::makeDirectory($repoDir, 0755, true);
        }

        $interfaceName = $name . 'RepositoryInterface';
        $repoName = $name . 'Repository';

        $interfacePath = $interfaceDir . '/' . $interfaceName . '.php';
        $repoPath = $repoDir . '/' . $repoName . '.php';

        // Create interface
        if (!File::exists($interfacePath)) {
            $interfaceTemplate = "<?php

namespace App\Repositories\Interfaces;

interface {$interfaceName}
{
    public function all();
    public function find(\$id);
    public function create(array \$data);
    public function update(\$id, array \$data);
    public function delete(\$id);
}
";
            File::put($interfacePath, $interfaceTemplate);
            $this->info("Interface {$interfaceName} created successfully.");
        } else {
            $this->warn("Interface {$interfaceName} already exists.");
        }

        // Create repository class
        if (!File::exists($repoPath)) {
            $repoTemplate = "<?php

namespace App\Repositories;

use App\Repositories\Interfaces\\{$interfaceName};

class {$repoName} implements {$interfaceName}
{
    public function all()
    {
        // TODO: implement
    }

    public function find(\$id)
    {
        // TODO: implement
    }

    public function create(array \$data)
    {
        // TODO: implement
    }

    public function update(\$id, array \$data)
    {
        // TODO: implement
    }

    public function delete(\$id)
    {
        // TODO: implement
    }
}
";
            File::put($repoPath, $repoTemplate);
            $this->info("Repository {$repoName} created successfully.");
        } else {
            $this->warn("Repository {$repoName} already exists.");
        }
    }
}
