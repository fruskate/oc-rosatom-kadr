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
        'fio' => 'required',
        'borned_at' => 'required',
        'started_at' => 'required'
    ];

    public $dates = ['borned_at', 'started_at', 'ended_at'];

    protected $appends = ['work_days'];

    protected $fillable = ['fio', 'borned_at', 'started_at', 'ended_at', 'is_ended', 'reasdis_id', 'salary',
        'childs', 'sex_id', 'position_id', 'family_id'];

    public $belongsTo = [
        'reasdis' => \Frukt\Kadr\Models\Reasdis::class,
        'sex' => \Frukt\Kadr\Models\Sex::class,
        'position' => \Frukt\Kadr\Models\Position::class,
        'family' => \Frukt\Kadr\Models\Family::class
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
        $end = $this->ended_at? $this->ended_at : Carbon::now();

        return $end->diffInDays($this->started_at);
    }
}
