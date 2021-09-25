<?php namespace Frukt\Kadr;

use System\Classes\PluginBase;
use Rubix\ML\Datasets\Labeled;
use Rubix\ML\Classifiers\KNearestNeighbors;

use Rubix\ML\Datasets\Unlabeled;

use Rubix\ML\CrossValidation\Metrics\MeanAbsoluteError;

use Illuminate\Database\Eloquent\Factory as EloquentFactory;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'НАСТРОЙКИ KADR',
                'description' => 'Управляйте настройками программы',
                'category'    => 'kadr',
                'icon'        => 'icon-cog',
                'class'       => 'Frukt\Kadr\Models\Settings',
                'order'       => 500,
                'keywords'    => 'settings kadr',
                'permissions' => []
            ]
        ];
    }

    public function register()
    {
        $this->registerConsoleCommand('dataset:entergroups', 'Frukt\Kadr\Console\EnterToGroups');
        $this->registerConsoleCommand('dataset:addconditions', 'Frukt\Kadr\Console\AddConditions');
        $this->registerConsoleCommand('make:demoset', 'Frukt\Kadr\Console\MakeDemo');

        app(EloquentFactory::class)->load(plugins_path('frukt/kadr/factories'));
    }

    public function boot()
    {
        /* $samples = [
            [3, 4, 50.5],
            [1, 5, 24.7],
            [4, 4, 62.0],
            [3, 2, 31.1],
        ];

        $labels = ['married', 'divorced', 'married', 'divorced'];

        $dataset = new Labeled($samples, $labels);
        $estimator = new KNearestNeighbors(3);

        $estimator->train($dataset);

        trace_log($estimator->trained());



        $samples = [
            [4, 3, 44.2],
            [2, 2, 100.7],
            [2, 4, 19.5],
            [3, 3, 55.0],
        ];

        $dataset = new Unlabeled($samples);

        $predictions = $estimator->predict($dataset);

        $probabilities = $estimator->proba($dataset);


        print_r($probabilities);

        print_r($predictions); */


    }
}
