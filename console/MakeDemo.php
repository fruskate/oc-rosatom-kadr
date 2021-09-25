<?php namespace Frukt\Kadr\Console;

use Frukt\Kadr\Models\History;
use Frukt\Kadr\Models\Specialist;
use Illuminate\Console\Command;
use Throwable;

class MakeDemo extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'make:demoset';

    /**
     * @var string The console command description.
     */
    protected $description = 'Захерачить демосет с разными данными';

    /**
     * Execute the console command.
     * @return void
     */

    /**
     * Get the console command arguments.
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    public function handle()
    {
        //factory(Specialist::class, 5000)->create();
        factory(History::class, 5000)->create();
        //Specialist::factory()->count(3)->create();
    }
}
