<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeService extends Command
{
    // The name and signature of the console command.
    protected $signature = 'make:service {name}';  // Accepts the name of the service

    // The console command description.
    protected $description = 'Create a new service class';

    protected $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    // The command logic
    public function handle()
    {
        // Get the service name from the input argument
        $name = $this->argument('name');

        // Determine the service class file path
        $path = app_path("Services/{$name}.php");

        // Check if the service already exists
        if ($this->files->exists($path)) {
            $this->error("Service {$name} already exists!");
            return;
        }

        // Create the directory if it doesn't exist
        $this->makeDirectory($path);

        // Generate the service class content
        $this->files->put($path, $this->getStubContent($name));

        // Output success message
        $this->info("Service {$name} created successfully.");
    }

    // Generate the service class content from a stub
    protected function getStubContent($name)
    {
        return <<<PHP
<?php

namespace App\Services;

class {$name}
{
    // Implement your service methods here
}
PHP;
    }

    // Create the directory for the service class if it doesn't exist
    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }
    }
}


