<?php

namespace NewJapanOrders\EmailActivation\Commands;

use Illuminate\Console\Command;
use NewJapanOrders\Stub\Stub;
use NewJapanOrders\Arguments\CompilableArguments;

class MigrationPublishCommand extends Command
{
    use CompilableArguments;

    /**
     * The console command name!
     *
     * @var string
     */
    protected $signature = 'email-activation:migration-publish {model_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    public function __construct()
    {   
        parent::__construct();
    }   

    /** 
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->getArguments();
 
        $this->copyMigrations(); 
    }

    protected function copyMigrations()
    {
        $stub_filepath = __DIR__.'/../database/migrations/2018_05_29_000000_add_activation.stub';
        $migration_filepath = base_path().'/database/migrations/2018_05_29_000000_add_activation_to_'.$this->model->singular_snake.'_model.php';

        if (file_exists($migration_filepath)) {
            $this->comment("[Warning]{$migration_filepath} file is already exists...skip");
        } else {   
            file_put_contents(
                $migration_filepath,
                $this->compileStub($stub_filepath)
            );
        }
    }
}
