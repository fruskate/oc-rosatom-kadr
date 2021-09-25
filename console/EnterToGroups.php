<?php namespace Frukt\Kadr\Console;

use Frukt\Kadr\Models\Specialist;
use Illuminate\Console\Command;
use Throwable;

class EnterToGroups extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'dataset:entergroups';

    /**
     * @var string The console command description.
     */
    protected $description = 'Захерачить группы датасету';

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
        $specialists = Specialist::where('age', '<=', 35)->doesntHave('groups')->get();


        $this->info('Будем обрабатывать '.$specialists->count().' молодых специалистов.');
        $bar = $this->output->createProgressBar($specialists->count());

        foreach ($specialists as $specialist) {
            $specialist->groups()->syncWithoutDetaching([1, $this->randomGroupOfEducation()]);

            $bar->advance();
        }

        $bar->finish();
        $this->info('Выполнено!');

        unset($specialists);

        $specialists = Specialist::where('age', '>', 35)->doesntHave('groups')->get();
        $this->info('Будем обрабатывать '.$specialists->count().' тех кто постарше.');
        $bar = $this->output->createProgressBar($specialists->count());

        foreach ($specialists as $specialist) {
            $specialist->groups()->syncWithoutDetaching([4, 7]);

            $bar->advance();
        }

        $bar->finish();
        $this->info('Выполнено!');

    }

    public function randomGroupOfEducation()
    {
        $ran = array(4, 5, 6);
        return $ran[array_rand($ran, 1)];
    }
}
