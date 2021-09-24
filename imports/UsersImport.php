<?php namespace Frukt\Kadr\Imports;

use Carbon\Carbon;
use Frukt\Kadr\Models\Group;
use Frukt\Kadr\Models\History;
use Frukt\Kadr\Models\Specialist;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    public function model(array $row)
    {
        $isEnded = (bool)$row[6];

        $specialist = Specialist::where('fio', $row[0])->first();

        if ($specialist) {
            if ($isEnded) {
                $specialist->is_ended = true;
                $specialist->reasdis_id = $this->randomReasdis();
                $specialist->ended_at = Carbon::parse($this->toNormalDate($row[6]));
                $specialist->save();

                if ($specialist->salary != $row[9]) {
                    if ($specialist->salary > $row[9]) {
                        $history = new History();
                        $history->specialist_id = $specialist->id;
                        $history->condition_id = 6; // Понижение зарплаты
                        $history->amount = $specialist->salary - $row[9];
                        $history->save();
                    } else {
                        $history = new History();
                        $history->specialist_id = $specialist->id;
                        $history->condition_id = 5; // Повышение зарплаты
                        $history->amount = $row[9] - $specialist->salary;
                        $history->save();
                    }
                }
            }
            return $specialist;
        } else {
            $specialist = new Specialist([
                'fio'        => $row[0],
                'borned_at'  => Carbon::parse($this->toNormalDate($row[2])),
                'started_at' => Carbon::parse($this->toNormalDate($row[5])),
                'ended_at'   => $isEnded ? Carbon::parse($this->toNormalDate($row[6])) : null,
                'is_ended'   => $isEnded,
                'reasdis_id' => $isEnded ? $this->randomReasdis() : null,
                'salary'     => $row[9],
            ]);
            $specialist->save();
        }

        $groupPosition = Group::where('parent_id', 11)->where('name', $row[1])->firstOrCreate([
            'name'      => $row[1],
            'parent_id' => 11
        ]);

        $groupSex = Group::where('parent_id', 8)->where('name', $row[3])->firstOrCreate([
            'name'      => $row[3],
            'parent_id' => 8
        ]);

        $groupFamily = Group::where('parent_id', 39)->where('name', $row[4])->firstOrCreate([
            'name'      => $row[4],
            'parent_id' => 39
        ]);
        $specialist->groups()->syncWithoutDetaching([$groupPosition->id, $groupSex->id, $groupFamily->id]);
        $history = new History();
        $history->specialist_id = $specialist->id;
        $history->condition_id = 7; // Детки
        $history->amount = $row[11];
        $history->created_at = Carbon::parse($this->toNormalDate($row[5]));
        $history->save();

        return $specialist;
    }

    public function toNormalDate($date)
    {
        return ($date - 25569) * 86400;
    }

    public function randomReasdis()
    {
        $ran = array(1, 1, 1, 1, 2);
        return $ran[array_rand($ran, 1)];
    }
}
