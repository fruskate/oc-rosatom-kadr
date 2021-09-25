<?php namespace Frukt\Kadr\Imports;

use Carbon\Carbon;
use Frukt\Kadr\Models\Family;
use Frukt\Kadr\Models\Group;
use Frukt\Kadr\Models\History;
use Frukt\Kadr\Models\Position;
use Frukt\Kadr\Models\Sex;
use Frukt\Kadr\Models\Specialist;
use Maatwebsite\Excel\Concerns\ToModel;

class MentorImport implements ToModel
{
    public function model(array $row)
    {
        $specialist = Specialist::where('fio', $row[0])->first();

        if ($specialist) {
            $history = new History();
            $history->specialist_id = $specialist->id;
            $history->condition_id = 1; // Наставник
            $history->amount = 1;
            $history->save();
        }

        return $specialist;
    }
}
