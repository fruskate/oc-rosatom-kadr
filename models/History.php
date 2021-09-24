<?php namespace Frukt\Kadr\Models;

use Model;

/**
 * Model
 */
class History extends Model
{
    use \October\Rain\Database\Traits\Validation;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'frukt_kadr_histories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $belongsTo = [
        'condition' => \Frukt\Kadr\Models\Condition::class,
        'specialist' => \Frukt\Kadr\Models\Specialist::class,
    ];

    public function afterCreate()
    {
        $salaryPlusId = Settings::get('salary_plus_id');
        $salaryMinusId = Settings::get('salary_minus_id');

        if (in_array($this->condition_id, [$salaryMinusId, $salaryPlusId])) {
            switch ($this->condition_id) {
                case $salaryPlusId:
                    $this->specialist->salary += $this->amount;
                    $this->specialist->save();
                    break;
                case $salaryMinusId:
                    $this->specialist->salary -= $this->amount;
                    $this->specialist->save();
                    break;
            }
        }

    }
}
