<?php

namespace App\Libraries;

use App\ModuleRepeatitionWeight;
use Illuminate\Support\Facades\DB;

/**
     * All functions related to reading weights from DB
     *
     * function getModuleRepetitionWeightForTaForModule( string $ta_email, string $module_id)
     *
     * function getOneModuleRepetitionWeight(int $times)
     *
     * function getWeightForModulePriority(int $priority)
     *
     * function getWeightsForAllModulePriorities()
     *
     * function getWeightsForAllModulePriorities()
     *
     * function object_to_array($data)
     *
     * function getWeightForOneLanguagePriority(int $language_priority)
     *
     * function getWeightForAllLanguagePriorities()
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
    function getModuleRepetitionWeightForTaForModule(string $ta_email, string $module_id)
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
        return DB::table('module_priority_weights')->where('type', '=', 'current')->get();
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
}
