<?php

namespace Myth\Api\Commands;

use Illuminate\Console\ConfirmableTrait;
use Illuminate\Database\Console\Migrations\TableGuesser;
use Illuminate\Support\Str;

class MakeTransformerCommand extends BaseCommand
{

    use ConfirmableTrait;
    /**
     * The console command signature.
     * @var string
     */
    protected $signature = 'myth:make-transformer {name : The name of the transformer}';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Create a new transformer of model file';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $name = Str::snake(trim($this->input->getArgument('name')));

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table, $create);

    }
}
