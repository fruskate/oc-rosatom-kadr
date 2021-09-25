<?php namespace Frukt\Kadr\Console;

use Frukt\Kadr\Models\History;
use Frukt\Kadr\Models\Specialist;
use Illuminate\Console\Command;
use Throwable;

class AddConditions extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'dataset:addconditions';

    /**
     * @var string The console command description.
     */
    protected $description = 'Захерачить условия датасету';

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

        $bar = $this->output->createProgressBar(5);

        $specialists = Specialist::where('is_ended', false)->inRandomOrder()->take(2300)->get();

        foreach ($specialists as $specialist) {
            $history = new History();
            $history->specialist_id = $specialist->id;
            $history->condition_id = 3; // бесплатный кофе
            $history->amount = 1;
            $history->save();
        }

        unset ($specialists);
        $bar->advance();

        $specialists = Specialist::where('is_ended', false)->inRandomOrder()->take(1264)->get();

        foreach ($specialists as $specialist) {
            $history = new History();
            $history->specialist_id = $specialist->id;
            $history->condition_id = 2; // Наличие компьютера
            $history->amount = 1;
            $history->save();
        }

        unset ($specialists);
        $bar->advance();

        $specialists = Specialist::where('is_ended', false)->inRandomOrder()->take(281)->get();

        foreach ($specialists as $specialist) {
            $history = new History();
            $history->specialist_id = $specialist->id;
            $history->condition_id = 9; // Премия
            $history->amount = 5000;
            $history->save();
        }

        unset ($specialists);
        $bar->advance();

        $specialists = Specialist::where('is_ended', true)->inRandomOrder()->take(15)->get();

        foreach ($specialists as $specialist) {
            $history = new History();
            $history->specialist_id = $specialist->id;
            $history->condition_id = 8; // Штраф
            $history->amount = 300;
            $history->save();
        }

        unset ($specialists);
        $bar->advance();

        $specialists = Specialist::where('is_ended', false)->inRandomOrder()->take(15)->get();

        foreach ($specialists as $specialist) {
            $history = new History();
            $history->specialist_id = $specialist->id;
            $history->condition_id = 4; // Бесплатное жильё
            $history->amount = 1;
            $history->save();
        }

        unset ($specialists);
        $bar->advance();


        $bar->finish();

    }
}
