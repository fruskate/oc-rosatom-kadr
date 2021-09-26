<?php namespace Frukt\Kadr\Models;

use Carbon\Carbon;
use Model;

/**
 * Model
 */
class Specialist extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'frukt_kadr_specialists';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'fio'        => 'required',
        'borned_at'  => 'required',
        'started_at' => 'required'
    ];

    public $dates = ['borned_at', 'started_at', 'ended_at'];

    protected $appends = ['work_days', 'vozrast', 'neural'];

    protected $fillable = ['fio', 'borned_at', 'started_at', 'ended_at', 'is_ended', 'reasdis_id', 'salary',
        'childs', 'sex_id', 'position_id', 'family_id'];

    public $belongsTo = [
        'reasdis'  => \Frukt\Kadr\Models\Reasdis::class,
        'sex'      => \Frukt\Kadr\Models\Sex::class,
        'position' => \Frukt\Kadr\Models\Position::class,
        'family'   => \Frukt\Kadr\Models\Family::class
    ];

    public $belongsToMany = [
        'groups' => [
            \Frukt\Kadr\Models\Group::class,
            'table' => 'frukt_kadr_specialist_group'
        ],
        /*'conditions' => [
            \Frukt\Kadr\Models\Condition::class,
            'table' => 'frukt_kadr_sp_cond'
        ] */
    ];

    public $hasMany = [
        'histories' => [
            \Frukt\Kadr\Models\History::class
        ],
    ];

    public function beforeUpdate()
    {
        if (!$this->is_ended) {
            $this->ended_at = null;
            $this->reasdis_id = null;
        }

        $this->age = $this->borned_at->diffInYears(\Carbon\Carbon::now());
    }

    public function beforeCreate()
    {
        $this->age = $this->borned_at->diffInYears(\Carbon\Carbon::now());
    }

    public function getWorkDaysAttribute()
    {
        $end = $this->ended_at ? $this->ended_at : Carbon::now();

        return $end->diffInDays($this->started_at);
    }

    public function getVozrastAttribute()
    {
        $end = $this->ended_at ? $this->ended_at : false;

        if (!$end) {
            return false;
        }

        return $end->diffInYears($this->borned_at);
    }

    public function getNeuralAttribute()
    {
        return [
            $this->work_days,
            $this->age,
            round($this->salary),
            $this->sex_id,
            $this->position_id,
            $this->family_id,
            $this->childs,
            $this->hasCondition(1),
            $this->hasCondition(2),
            $this->hasCondition(3),
            $this->hasCondition(4),
            $this->hasCondition(5),
            $this->hasCondition(6),
            $this->hasCondition(7),
            $this->hasCondition(8),
            $this->hasCondition(9),
        ];
    }

    public function getNeuralMonthAttribute()
    {
        return [
            $this->work_days + 30,
            $this->age,
            round($this->salary),
            $this->sex_id,
            $this->position_id,
            $this->family_id,
            $this->childs,
            $this->hasCondition(1),
            $this->hasCondition(2),
            $this->hasCondition(3),
            $this->hasCondition(4),
            $this->hasCondition(5),
            $this->hasCondition(6),
            $this->hasCondition(7),
            $this->hasCondition(8),
            $this->hasCondition(9),
        ];
    }

    public function getNeuralWeekAttribute()
    {
        return [
            $this->work_days + 7,
            $this->age,
            round($this->salary),
            $this->sex_id,
            $this->position_id,
            $this->family_id,
            $this->childs,
            $this->hasCondition(1),
            $this->hasCondition(2),
            $this->hasCondition(3),
            $this->hasCondition(4),
            $this->hasCondition(5),
            $this->hasCondition(6),
            $this->hasCondition(7),
            $this->hasCondition(8),
            $this->hasCondition(9),
        ];
    }

    public function getNeuralTwoWeeksAttribute()
    {
        return [
            $this->work_days + 14,
            $this->age,
            round($this->salary),
            $this->sex_id,
            $this->position_id,
            $this->family_id,
            $this->childs,
            $this->hasCondition(1),
            $this->hasCondition(2),
            $this->hasCondition(3),
            $this->hasCondition(4),
            $this->hasCondition(5),
            $this->hasCondition(6),
            $this->hasCondition(7),
            $this->hasCondition(8),
            $this->hasCondition(9),
        ];
    }

    public function hasCondition($condition_id) {
        if ($this->histories()->where('condition_id', $condition_id)->first()) {
            return 1;
        } else {
            return 0;
        }
    }
}
