<?php namespace Frukt\Kadr\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Carbon\Carbon;
use Frukt\Kadr\Models\Group;
use Frukt\Kadr\Models\Settings;
use Frukt\Kadr\Models\Specialist;

class Specialists extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\RelationController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';

    public $requiredPermissions = [
        'specialist'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Frukt.Kadr', 'main-menu-item', 'side-menu-item');
    }

    public function index() {
        parent::index();

        $this->addJs('/plugins/frukt/kadr/assets/js/chart.min.js', '1.0.0');

        $specialists = Specialist::where('is_ended', false)->count();
        $newbie = Specialist::where('is_ended', false)->whereHas('groups', function($query) {
            $query->where('code', 'newbie');
        })->count();
        $notNewbie = Specialist::where('is_ended', false)->whereDoesntHave('groups', function($query) {
            $query->where('code', 'newbie');
        })->count();
        $endedNewbieSam = Specialist::whereHas('groups', function($query) {
            $query->where('code', 'newbie');
        })->where('is_ended', true)->where('reasdis_id', 1)->count();;
        $endedAll = Specialist::where('is_ended', true)->count();

        $this->vars['allStat'] = [
            'all' => $specialists,
            'newbie' => $newbie,
            'notNewbie' => $notNewbie,

            'endedNewbieSam' => $endedNewbieSam,
            'endedAll' => $endedAll,
        ];
    }

    public function listInjectRowClass($specialist, $definition = null)
    {
        // Strike through past lessons
        if ($specialist->is_ended) {
            return 'strike';
        }
    }

    public function onLoadPieAll()
    {
        $today = Carbon::now();

        $plus = Specialist::where('started_at', '>', $today->copy()->subDays(30)->toDateString())
            ->count();
        $minus = Specialist::where('is_ended', true)
            ->where('ended_at', '>', $today->copy()->subDays(30)->toDateString())
            ->count();

        $returnArray = [
            'data' => [
                'labels' => ['Устроилось', 'Уволилось'],
                'datasets' => array([
                    'backgroundColor' => ['#1ce018', '#d61111'],
                    'data' => [$plus, $minus],
                    'hoverOffset' => 4
                ])
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'За последний месяц'
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ],

        ];

        return $returnArray;

    }

    public function onLoadProcentEndedNewbies()
    {
        $today = Carbon::now();

        $i = 1;
        $arrayWithPercentage = array();
        $monthToDisplay = Settings::get('month_to_specialists_display', 6);
        while ($i <= $monthToDisplay) {
            $min = $today->copy()->subMonths($i);
            $max = $today->copy()->subMonths($i-1);
            $lastMonthNewbie = Specialist::where('started_at', '<', $max->toDateString())
                ->where('ended_at', '>', $min->toDateString())
                ->where('ended_at', '<', $max->toDateString())
                ->where('is_ended', true)
                ->whereHas('groups', function($query) {
                    $query->where('code', 'newbie');
                })->where('reasdis_id', 1)
                ->count();
            $lastMonthAll = Specialist::where('started_at', '<', $max->toDateString())
                ->where('ended_at', '>', $min->toDateString())
                ->where('ended_at', '<', $max->toDateString())
                ->where('is_ended', true)
                ->where('reasdis_id', 1)
                ->count();
            $arrayWithPercentage[] = ($lastMonthAll > 0)? round($lastMonthNewbie * 100 / $lastMonthAll) : 0;
            $labels[] = $max->format('M Y');

            $i++;
        }

        $returnArray = [
            'data' => [
                'labels' => array_reverse($labels),
                'datasets' => array([
                    'data' => array_reverse($arrayWithPercentage),
                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.5
                ])
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => '% увольнения сотрудников без опыта'
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ],

        ];

        return $returnArray;

    }

    public function onLoadGroupConters()
    {
        $groups = Group::whereDoesntHave('children')->get();

        foreach ($groups as $group) {
            $labels[] = $group->name;
            $data[] = $group->specialists()->where('is_ended', false)->count();
            $backgroundColor[] = $group->color;
        }

        $specialistsWithoutGroups = Specialist::whereDoesntHave('groups')->where('is_ended', false)->count();
        $labels[] = 'Группы отсутствуют';
        $data[] = $specialistsWithoutGroups;
        $backgroundColor[] = '#f407e8';

        $returnArray = [
            'data' => [
                'labels' => $labels,
                'datasets' => array([
                    'backgroundColor' => $backgroundColor,
                    'data' => $data
                ])
            ],
            'options' => [
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Всего работающих сотрудников по группам сейчас'
                    ],
                    'legend' => [
                        'display' => false
                    ]
                ]
            ],

        ];

        return $returnArray;
    }
}
