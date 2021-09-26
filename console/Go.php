<?php namespace Frukt\Kadr\Console;

use Frukt\Kadr\Models\Specialist;
use Illuminate\Console\Command;
use Throwable;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Classifiers\KNearestNeighbors;
use Rubix\ML\CrossValidation\Metrics\Accuracy;

use Rubix\ML\Datasets\Unlabeled;

use Rubix\ML\CrossValidation\Metrics\MeanAbsoluteError;

class Go extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'ml:go';

    /**
     * @var string The console command description.
     */
    protected $description = 'Команда для запуска нейронной сети';

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
        $labels = array();
        $samples = array();

        $spec = Specialist::where('id', '>', 1)->get();

        foreach ($spec as $item) {
            //trace_log($item->neural);
            if ($item->is_ended) {
                $labels[] = 'rester';
            } else {
                $labels[] = 'worker';
            }
            $samples[] = $item->neural;
        }

        //trace_log($samples, $labels);

        $dataset = new Labeled($samples, $labels);
        //
        $estimator = new KNearestNeighbors(3);

        $estimator->train($dataset);

        //$predictions = $estimator->predict($testing);

        //$metric = new Accuracy();

        //$score = $metric->score($predictions, $testing->labels());

        //$this->info('Точность '.$score);

        $specialists = Specialist::where('is_ended', false)->get();
        $samples = array();
        $samples2 = array();
        $samples3 = array();
        foreach ($specialists as $item) {
            $samples[] = $item->neural_month;
            $samples2[] = $item->neural_week;
            $samples3[] = $item->neural_two_weeks;
        }

        $dataset = new Unlabeled($samples);
        $dataset2 = new Unlabeled($samples2);
        $dataset3 = new Unlabeled($samples3);

        $predictions = $estimator->predict($dataset);
        $predictions2 = $estimator->predict($dataset2);
        $predictions3 = $estimator->predict($dataset3);

        $probabilities = $estimator->proba($dataset);
        $probabilities2 = $estimator->proba($dataset2);
        $probabilities3 = $estimator->proba($dataset3);

        $i = 0;
        foreach ($specialists as $item) {
            $item->pcnt_month = round($probabilities[$i]['rester'] * 100);
            $item->pcnt_week = round($probabilities2[$i]['rester'] * 100);
            $item->pcnt_two_weeks = round($probabilities3[$i]['rester'] * 100);
            $item->save();
            $i++;
        }
    }
}
