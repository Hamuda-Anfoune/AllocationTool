<?php

namespace App\Libraries;

use App\ModuleRepeatitionWeight;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

/**
    * ONLY USE TO READ WEIGHTS FROM DB!. SHOULD NOT USE ANY OTHER CUSTOM CLASS.
    * All functions related to reading weights from DB.
    *
    * @method int calculateModuleRepetitionWeightForTaForModule( string $ta_email, string $module_id)
    *
    * @method int getOneModuleRepetitionWeight(int $times)
    *
    * @method getWeightForModulePriority(int $priority)
    *
    * @method getWeightsForAllModulePriorities()
    *
    * @method getWeightsForAllModulePriorities()
    *
    * @method object_to_array($data)
    *
    * @method getWeightForOneLanguagePriority(int $language_priority)
    *
    * @method getWeightForAllLanguagePriorities()
*/
class WeightsClass
{
    /**
     * Send ta_email && module_id.
     * Will calculate how many times a TA has assisted with module and return the equivilant weight for that
     *
     * @param  int  $times of repetition
     * @return int  weight of repetition times
     */
    function calculateRepetitionWeightForModuleForTa(string $ta_email, string $module_id)
    {
        /* TODO: edit $times to calculate the repetiotion times in previous allocations where TA was allocated to Module
         *
         */

        $times  = 1;  // should be changed later in the future

        if($times >= 5)
        {
            return DB::table('module_repeatition_weights')->select('repeated_times_5')->where('type', '=', 'current')->first()->repeated_times_5;
        }
        else
        {
            switch ($times)
            {
                case 0:
                    return 0;
                    break;
                case 1:
                    return DB::table('module_repeatition_weights')->select('repeated_times_1')->where('type', '=', 'current')->first()->repeated_times_1;
                    break;
                case 2:
                    return DB::table('module_repeatition_weights')->select('repeated_times_2')->where('type', '=', 'current')->first()->repeated_times_2;
                    break;
                case 3:
                    return DB::table('module_repeatition_weights')->select('repeated_times_3')->where('type', '=', 'current')->first()->repeated_times_3;
                    break;
                case 4:
                    return DB::table('module_repeatition_weights')->select('repeated_times_4')->where('type', '=', 'current')->first()->repeated_times_4;
                    break;
            }
        }
    }

    function getOneModuleRepetitionWeight(int $times)
    {
        if($times >= 5)
        {
            return DB::table('module_repeatition_weights')->select('repeated_times_5')->where('type', '=', 'current')->first()->repeated_times_5;
        }
        else
        {
            switch ($times)
            {
                case 0:
                    return 0;
                    break;
                case 1:
                    return DB::table('module_repeatition_weights')->select('repeated_times_1')->where('type', '=', 'current')->first()->repeated_times_1;
                    break;
                case 2:
                    return DB::table('module_repeatition_weights')->select('repeated_times_2')->where('type', '=', 'current')->first()->repeated_times_2;
                    break;
                case 3:
                    return DB::table('module_repeatition_weights')->select('repeated_times_3')->where('type', '=', 'current')->first()->repeated_times_3;
                    break;
                case 4:
                    return DB::table('module_repeatition_weights')->select('repeated_times_4')->where('type', '=', 'current')->first()->repeated_times_4;
                    break;
            }
        }

    }

    function getAllCurrentModuleRepetitionWeights()
    {
        return DB::table('module_repeatition_weights')
                    ->select('repeated_times_1', 'repeated_times_2', 'repeated_times_3', 'repeated_times_4', 'repeated_times_5')
                    ->where('type', '=', 'current')
                    ->get();
    }
    /**
     * Give the weight according to the priority of the module in the TA's ROL
     *
     * @param  int  $priority takes (1-10)
     * @return int: weight
     */
    function getWeightForModulePriority(int $priority)
    {
        $obj = DB::table('module_priority_weights')->where('type', '=', 'current')->first();

        if($priority >= 10)
        {
            return $obj->module_weight_10;
        }
        else
        {
            switch ($priority)
            {
                case 0:
                    return 0;
                    break;
                case 1:
                    return $obj->module_weight_1;
                    break;
                case 2:
                    return $obj->module_weight_2;
                    break;
                case 3:
                    return $obj->module_weight_3;
                    break;
                case 4:
                    return $obj->module_weight_4;
                    break;
                case 5:
                    return $obj->module_weight_5;
                    break;
                case 6:
                    return $obj->module_weight_6;
                    break;
                case 7:
                    return $obj->module_weight_7;
                    break;
                case 8:
                    return $obj->module_weight_8;
                    break;
                case 9:
                    return $obj->module_weight_9;
                    break;
            }
        }
    } // end of function

    function getWeightsForAllModulePriorities()
    {
        return DB::table('module_priority_weights')
                ->select('module_weight_1', 'module_weight_2', 'module_weight_3', 'module_weight_4','module_weight_5',
                        'module_weight_6', 'module_weight_7', 'module_weight_8', 'module_weight_9','module_weight_10')
                ->where('type', '=', 'current')
                ->get();
    }

    function object_to_array($data)
    {
        if (is_array($data) || is_object($data))
        {
            $result = array();
            foreach ($data as $key => $value)
            {
                $result[$key] = $this->object_to_array($value);
            }
            return $result;
        }
        return $data;
    }

    function getWeightForOneLanguagePriority(int $language_priority)
    {
        return DB::table('language_weights')
                    ->select('weight')
                    ->where('order','=', $language_priority)
                    ->where('type','=', 'current')
                    ->first()->weight;
    }

    /**
     * Returns all weights for all programming language priorities.
     *
     * @param
     * @return array
     * @return key => order : the priority
     * @return value => value : the weight for that priority order
     *
     * example: $array[1]: will give the weight of the language being the top language pririty for the module
     */
    function getWeightForAllLanguagePriorities()
    {
        $basic = DB::table('language_weights')->where('type', '=', 'current')->get()->toArray();
        return array_column($basic, 'weight', 'order');
    }

    function updateModulePriorityWeights($req)
    {
        return DB::table('module_priority_weights')
                    ->where('type', 'current')
                    ->update(
                        [
                            'type' => 'current',
                            'module_weight_1' => $req['module_priority_weight_1'],
                            'module_weight_2' => $req['module_priority_weight_2'],
                            'module_weight_3' => $req['module_priority_weight_3'],
                            'module_weight_4' => $req['module_priority_weight_4'],
                            'module_weight_5' => $req['module_priority_weight_5'],
                            'module_weight_6' => $req['module_priority_weight_6'],
                            'module_weight_7' => $req['module_priority_weight_7'],
                            'module_weight_8' => $req['module_priority_weight_8'],
                            'module_weight_9' => $req['module_priority_weight_9'],
                            'module_weight_10' => $req['module_priority_weight_10'],
                        ]
                    );
    }

    /**
     * Resets the MOdule Priority Weights to default values
     * Default values are read from the database
     */
    function resetModulePriorityWeights()
    {
        $default = DB::table('module_priority_weights')
                        ->select('module_weight_1', 'module_weight_2', 'module_weight_3', 'module_weight_4', 'module_weight_5',
                                 'module_weight_6', 'module_weight_7', 'module_weight_8', 'module_weight_9', 'module_weight_10')
                        ->where('type', 'default')
                        ->first();

         return DB::table('module_priority_weights')
                    ->where('type', 'current')
                    ->update(
                    [
                        'type' => 'current',
                        'module_weight_1' => $default->module_weight_1,
                        'module_weight_2' => $default->module_weight_2,
                        'module_weight_3' => $default->module_weight_3,
                        'module_weight_4' => $default->module_weight_4,
                        'module_weight_5' => $default->module_weight_5,
                        'module_weight_6' => $default->module_weight_6,
                        'module_weight_7' => $default->module_weight_7,
                        'module_weight_8' => $default->module_weight_8,
                        'module_weight_9' => $default->module_weight_9,
                        'module_weight_10' => $default->module_weight_10,
                    ]
                );
    }

    function updateLanguageWeights($req)
    {
        try
        {
            for($i = 1; $i <= 5; $i++)
            {
                DB::table('language_weights')
                        ->where('type', 'current')
                        ->where('order', '=', $i)
                        ->update(
                            [
                                'weight' => $req['language_weight_'.$i]
                            ]
                            );
            }
            return true;
        }
        catch(QueryException $qe)
        {
            return false;
        }
    }

    function resetLanguageWeights()
    {
        $defaults = DB::table('language_weights')
                        ->select('weight', 'order')
                        ->where('type', 'default')
                        ->get();
        try
        {
            foreach ($defaults as $default) {
                DB::table('language_weights')
                    ->where('type', 'current')
                    ->where('order', '=', $default->order)
                    ->update(
                        [
                            'weight' => $default->weight,
                        ]
                    );
            }
            return true;
        }
        catch(QueryException $qe)
        {
            return false;
        }
    }


}
