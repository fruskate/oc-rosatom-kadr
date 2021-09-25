<?php namespace Frukt\Kadr\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use Carbon\Carbon;
use Frukt\Kadr\Models\Condition;
use Frukt\Kadr\Models\Group;
use Frukt\Kadr\Models\Reasdis;
use Frukt\Kadr\Models\Specialist;
use function Matrix\trace;

/**
 * Analitycs Backend Controller
 */
class Analitycs extends Controller
{

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('Frukt.Kadr', 'main-menu-item', 'side-menu-item2');
    }

    public function index()
    {

    }

    public function correlation()
    {
        $this->addJs('/plugins/frukt/kadr/assets/js/datepicker.min.js');
        $this->addJs('/plugins/frukt/kadr/assets/js/correlation.js');
        $this->addJs('/plugins/frukt/kadr/assets/js/chart.min.js', '1.0.0');
        $this->addCss('/plugins/frukt/kadr/assets/css/datepicker.min.css');

        $this->vars['reasdises'] = Reasdis::all();
        $this->vars['groups'] = Group::all();
        $this->vars['conditions'] = Condition::all();
    }

    public function fluidity()
    {
        $this->addJs('/plugins/frukt/kadr/assets/js/datepicker.min.js');
        $this->addJs('/plugins/frukt/kadr/assets/js/chart.min.js', '1.0.0');
        $this->vars['reasdises'] = Reasdis::all();
        $this->vars['groups'] = Group::all();
        $this->vars['conditions'] = Condition::all();
    }

    public function onMakeCorrelation()
    {
        try {
            list($started, $ended) = explode('|', post('dates'));

            $started = Carbon::parse($started)->toDateTimeString();
            $ended = Carbon::parse($ended)->toDateTimeString();
        } catch (\Exception $exception) {
            throw new \ValidationException(['dates' => 'Пожалуйста, выберите даты!']);
        }

        $conditions = Condition::whereIn('id', post('conditions'))->get();

        $conditionsFinalList = array();

        foreach ($conditions as $condition) {
            $conditionsFinalList[] = ['name' => $condition->name];
        }

        $groups = Group::whereIn('id', post('groups'))->get();

        $arrayOfGroups = array();

        foreach ($groups as $group) {
                list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
                $arrayOfGroups[$group->id] = [
                    'name' => $group->name,
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => array()
                ];
            foreach ($conditions as $condition) {
                $arrayOfGroups[$group->id]['count'][] = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereHas('histories', function ($query) use ($condition) {
                            $query->where('condition_id', $condition->id);
                        })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->count();
            }
        }

        $isShowAverages = false;
        $averages = array();
        $averageTitles = array();

        if (post('salary') or post('stazh') or post('vozrast')) {
            $isShowAverages = true;
            if (post('salary')) {
                $averageTitles[] = 'Средняя зарплата';
            }
            if (post('stazh')) {
                $averageTitles[] = 'Стаж в годах';
            }
            if (post('vozrast')) {
                $averageTitles[] = 'Возраст';
            }

        }
        if ($isShowAverages) {

            foreach ($groups as $group) {
                list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");

                $counts = array();

                if (post('salary')) {
                    $salary = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->sum('salary');

                    $charsCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->count();
                    $averagePay = ($charsCount > 0)? round($salary / $charsCount): 0;


                    $counts[] = $averagePay;


                }

                if (post('stazh')) {
                    $allStazh = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->get()->sum('work_days') / 365;

                    $charsCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->count();

                    $averageStazh = ($charsCount > 0)? round($allStazh / $charsCount): 0;

                    $counts[] = $averageStazh;
                }
                if (post('vozrast')) {
                    $allVozrast = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->get()->sum('vozrast');

                    $charsCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->where('ended_at', '>=', $started)
                        ->where('ended_at', '<=', $ended)
                        ->count();

                    $averageVozrast = ($charsCount > 0)? round($allVozrast / $charsCount): 0;

                    $counts[] = $averageVozrast;
                }
                $averages[] = [
                    'name' => $group->name,
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => $counts,
                ];

            }

        }

        return [
            '#answer' => \Twig::parse($this->makePartial('make_correlation'), [
                'conditions' => $conditionsFinalList,
                'groups'    => $arrayOfGroups,
                'averages' => $averages,
                'averageTitles' => $averageTitles,
                'isShowAverages' => $isShowAverages,
            ]),
        ];
    }

    public function onMakeFluidity()
    {
        trace_log(post());
        $year = Carbon::parse(post('year').'-01-01')->startOfYear();
        $groups = Group::whereIn('id', post('groups'))->get();
        $dataset = array();

        foreach ($groups as $group) {
            list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
            $counts = array();
            for($i = 0; $i <= 11; $i++) {
                $from = $year->copy()->addMonths($i)->startOfMonth()->toDateTimeString();
                $to = $year->copy()->addMonths($i)->endOfMonth()->toDateTimeString();

                $all = Specialist::whereHas('groups', function ($query) use ($group) {
                    $query->where('id', $group->id);
                })
                    ->where('started_at', '<=', $to)
                    ->where('ended_at', '>', $to)
                    ->orWhere('started_at', '<=', $to)
                    ->whereNull('ended_at')
                    ->count();

                $ended = Specialist::whereHas('groups', function ($query) use ($group) {
                    $query->where('id', $group->id);
                })
                    ->where('started_at', '<=', $to)
                    ->where('ended_at', '<=', $to)
                    ->where('ended_at', '>=', $from)
                    ->whereIn('reasdis_id', post('reasdises'))
                    ->where('is_ended', true)
                    ->count();

                $ktk = $ended * 100 / $all;

                trace_log($from, $to, $all, $ended);

                $counts[] = $ktk;
            }
            $dataset[] = [
                'name' => $group->name,
                'color' => [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                ],
                'count' => $counts,
            ];
        }

        $months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];

        trace_log($dataset);
        return [
            '#answer' => \Twig::parse($this->makePartial('make_fluidity'), [
                'months' => $months,
                'dataset' => $dataset
            ]),
        ];
    }



}
