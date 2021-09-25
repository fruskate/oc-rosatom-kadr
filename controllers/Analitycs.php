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
                'name'  => $group->name,
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
                    $averagePay = ($charsCount > 0) ? round($salary / $charsCount) : 0;


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

                    $averageStazh = ($charsCount > 0) ? round($allStazh / $charsCount) : 0;

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

                    $averageVozrast = ($charsCount > 0) ? round($allVozrast / $charsCount) : 0;

                    $counts[] = $averageVozrast;
                }
                $averages[] = [
                    'name'  => $group->name,
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
                'conditions'     => $conditionsFinalList,
                'groups'         => $arrayOfGroups,
                'averages'       => $averages,
                'averageTitles'  => $averageTitles,
                'isShowAverages' => $isShowAverages,
            ]),
        ];
    }

    public function onMakeFluidity()
    {
        trace_log(post());
        $year = Carbon::parse(post('year') . '-01-01')->startOfYear();
        $groups = Group::whereIn('id', post('groups'))->get();
        $months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        $dataset = array();
        $dataset2 = array();

        foreach ($groups as $group) {
            list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
            $counts = array();
            for ($i = 0; $i <= 11; $i++) {
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
                'name'  => $group->name,
                'color' => [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                ],
                'count' => $counts,
            ];
        }


        $condition = Condition::find(post('condition_id'));
        $totalWithConditionYear = 0;
        $totalWithoutConditionYear = 0;
        foreach ($groups as $group) {
            $condition_id = post('condition_id');
            list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
            $counts1 = array();
            $counts2 = array();
            for ($i = 0; $i <= 11; $i++) {
                $from = $year->copy()->addMonths($i)->startOfMonth()->toDateTimeString();
                $to = $year->copy()->addMonths($i)->endOfMonth()->toDateTimeString();

                $dataWith = Specialist::whereHas('groups', function ($query) use ($group) {
                    $query->where('id', $group->id);
                })
                    ->whereHas('histories', function ($query) use ($condition_id) {
                        $query->where('condition_id', $condition_id);
                    })
                    ->where('started_at', '<=', $to)
                    ->where('ended_at', '<=', $to)
                    ->where('ended_at', '>=', $from)
                    ->whereIn('reasdis_id', post('reasdises'))
                    ->where('is_ended', true)
                    ->count();

                $dataWithOut = Specialist::whereHas('groups', function ($query) use ($group) {
                    $query->where('id', $group->id);
                })
                    ->whereHas('histories', function ($query) use ($condition_id) {
                        $query->where('condition_id', '<>', $condition_id);
                    })
                    ->where('started_at', '<=', $to)
                    ->where('ended_at', '<=', $to)
                    ->where('ended_at', '>=', $from)
                    ->whereIn('reasdis_id', post('reasdises'))
                    ->where('is_ended', true)
                    ->count();

                $counts1[] = $dataWith;
                $totalWithConditionYear += $dataWith;
                $counts2[] = $dataWithOut;
                $totalWithoutConditionYear += $dataWithOut;
            }
            $dataset2[] = [
                'name'  => $group->name . ' | включая ' . $condition->name,
                'color' => [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                ],
                'count' => $counts1,
            ];
            $dataset2[] = [
                'name'  => $group->name . ' | исключая ' . $condition->name,
                'color' => [
                    'r' => $r,
                    'g' => $g,
                    'b' => $b,
                ],
                'count' => $counts2,
            ];
        }

        $showSalary = false;
        $dataset3 = array();
        $totalWorkersSalary = 0;
        $totalFuckersSalary = 0;
        if (post('salary')) {
            $showSalary = true;
            foreach ($groups as $group) {
                list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
                $counts1 = array();
                $counts2 = array();
                for ($i = 0; $i <= 11; $i++) {
                    $from = $year->copy()->addMonths($i)->startOfMonth()->toDateTimeString();
                    $to = $year->copy()->addMonths($i)->endOfMonth()->toDateTimeString();

                    $salaryWorkers = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '>', $to)
                        ->orWhere('started_at', '<=', $to)
                        ->whereNull('ended_at')
                        ->sum('salary');

                    $salaryWorkersCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '>', $to)
                        ->orWhere('started_at', '<=', $to)
                        ->whereNull('ended_at')
                        ->count();

                    $salaryFuckers = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '<=', $to)
                        ->where('ended_at', '>=', $from)
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->sum('salary');

                    $salaryFuckersCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '<=', $to)
                        ->where('ended_at', '>=', $from)
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->count();

                    $counts1[] = ($salaryWorkersCount > 0) ? round($salaryWorkers / $salaryWorkersCount) : 0;
                    $totalWorkersSalary += ($salaryWorkersCount > 0) ? round($salaryWorkers / $salaryWorkersCount) : 0;
                    $counts2[] = ($salaryFuckersCount > 0) ? round($salaryFuckers / $salaryFuckersCount) : 0;
                    $totalFuckersSalary += ($salaryFuckersCount > 0) ? round($salaryFuckers / $salaryFuckersCount) : 0;
                }
                $dataset3[] = [
                    'name'  => $group->name . ' - работающие ',
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => $counts1,
                ];
                $dataset3[] = [
                    'name'  => $group->name . ' - уволенные',
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => $counts2,
                ];
            }
        }

        $showStazh = false;
        $dataset4 = array();
        $totalWorkersStazh = 0;
        $totalFuckersStazh = 0;
        if (post('stazh')) {
            $showStazh = true;
            foreach ($groups as $group) {
                list($r, $g, $b) = sscanf($group->color, "#%02x%02x%02x");
                $counts1 = array();
                $counts2 = array();
                for ($i = 0; $i <= 11; $i++) {
                    $from = $year->copy()->addMonths($i)->startOfMonth()->toDateTimeString();
                    $to = $year->copy()->addMonths($i)->endOfMonth()->toDateTimeString();

                    $stazhWorkers = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '>', $to)
                        ->orWhere('started_at', '<=', $to)
                        ->whereNull('ended_at')
                        ->get()->sum('work_days') / 365;

                    $stazhWorkersCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '>', $to)
                        ->orWhere('started_at', '<=', $to)
                        ->whereNull('ended_at')
                        ->count();

                    $stazhFuckers = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '<=', $to)
                        ->where('ended_at', '>=', $from)
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->get()->sum('work_days') / 365;

                    $stazhFuckersCount = Specialist::whereHas('groups', function ($query) use ($group) {
                        $query->where('id', $group->id);
                    })
                        ->where('started_at', '<=', $to)
                        ->where('ended_at', '<=', $to)
                        ->where('ended_at', '>=', $from)
                        ->whereIn('reasdis_id', post('reasdises'))
                        ->where('is_ended', true)
                        ->count();

                    $counts1[] = ($stazhWorkersCount > 0) ? round($stazhWorkers / $stazhWorkersCount) : 0;
                    $totalWorkersStazh += ($stazhWorkersCount > 0) ? round($stazhWorkers / $stazhWorkersCount) : 0;
                    $counts2[] = ($stazhFuckersCount > 0) ? round($stazhFuckers / $stazhFuckersCount) : 0;
                    $totalFuckersStazh += ($stazhFuckersCount > 0) ? round($stazhFuckers / $stazhFuckersCount) : 0;
                }
                $dataset4[] = [
                    'name'  => $group->name . ' - работающие ',
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => $counts1,
                ];
                $dataset4[] = [
                    'name'  => $group->name . ' - уволенные',
                    'color' => [
                        'r' => $r,
                        'g' => $g,
                        'b' => $b,
                    ],
                    'count' => $counts2,
                ];
            }
        }

        return [
            '#answer' => \Twig::parse($this->makePartial('make_fluidity'), [
                'months'                    => $months,
                'dataset'                   => $dataset,
                'dataset2'                  => $dataset2,
                'condition'                 => $condition,
                'totalWithConditionYear'    => $totalWithConditionYear,
                'totalWithoutConditionYear' => $totalWithoutConditionYear,
                'showSalary'                => $showSalary,
                'dataset3'                  => $dataset3,
                'totalWorkersSalary'        => round($totalWorkersSalary / 12 / $groups->count()),
                'totalFuckersSalary'        => round($totalFuckersSalary / 12 / $groups->count()),
                'showStazh'                => $showStazh,
                'dataset4'                  => $dataset4,
                'totalWorkersStazh'        => round($totalWorkersStazh / 12 / $groups->count()),
                'totalFuckersStazh'        => round($totalFuckersStazh / 12 / $groups->count())
            ]),
        ];
    }


}
