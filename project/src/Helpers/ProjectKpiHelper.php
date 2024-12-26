<?php
namespace Tritiyo\Project\Helpers;
use \Tritiyo\Project\Models\TargetProjectKpi;

class ProjectKpiHelper {

    public static function checkInRange($start_date, $end_date, $date_check) {
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        $check = strtotime($date_check);
        // Check that check date is between start & end
        //return (($start <= $check ) && ($check <= $end));
        return (($check <= $end));
    }


    /* */
    public static function getTargetKey($target_range, $year, $project_id, $meta_key){
        $data = TargetProjectKpi::where('target_range', $target_range)
                        ->where('year', $year)
                        ->where('project_id', $project_id)
                        ->where('meta_key', $meta_key)
                        ->first();
        return $data;
    }


    public static function getCalculate($counting_type, $target, $achive, $mark){
        if($counting_type == 'Reverse'){

            $point = ($target-$achive)*($mark/$target)+$mark;
            if($mark > $point){
                return ceil($point);
            } else {
                return ceil($mark);
            }

        } elseif($counting_type == 'Forward'){

            $point = ($achive-$target)*($mark/$target)+$mark;
            if($mark > $point){
                return ceil($point);
            } else {
                return ceil($mark);
            }

        } else {
            return 0;
        }
    }



    public static function getBonus($range, $year, $project_id, $value){
        if (($value >= 85) && ($value <= 89)) {
            $get = Self::getTargetKey($range, $year, $project_id, 'bonus_85_89');
            return $get->meta_value;
        } elseif (($value >= 90) && ($value <= 94)) {
            $get = Self::getTargetKey($range, $year, $project_id, 'bonus_90_94');
            return $get->meta_value;
        } elseif (($value >= 95) && ($value <= 100)) {
            $get = Self::getTargetKey($range, $year, $project_id, 'bonus_95_100');
            return $get->meta_value;
        } else {
            return 0;
        }
    }


}
