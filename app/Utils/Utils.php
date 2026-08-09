<?php

namespace App\Utils;

class Utils
{
    public static function windDirToStr($winDir, $windSpeed) {
        $windStr = '';
        if ($winDir!=null) {
            if ($windSpeed > 0) {
                if (($winDir>=337.5) || ($winDir<22.5)) {
                    $windStr = __('messages.wind_dir.n');
                };
                if (($winDir>=22.5) && ($winDir<67.5)){
                    $windStr = __('messages.wind_dir.ne');
                };
                if (($winDir>=67.5) && ($winDir<112.5)){
                    $windStr = __('messages.wind_dir.e');
                };
                if (($winDir>=112.5) && ($winDir<157.5)){
                    $windStr = __('messages.wind_dir.e');
                };
                if (($winDir>=157.5) && ($winDir<202.5)){
                    $windStr = __('messages.wind_dir.s');
                };
                if (($winDir>=202.5) && ($winDir<247.5)){
                    $windStr = __('messages.wind_dir.sw');
                };
                if (($winDir>=247.5) && ($winDir<292.5)){
                    $windStr = __('messages.wind_dir.w');
                };
                if (($winDir>=292.5) && ($winDir<337.5)){
                    $windStr = __('messages.wind_dir.nw');
                }
            } else {
                $windStr = __('messages.wind_dir.calm');
            }
        }
        return $windStr;
    }

    public static function wetBulb() {
        return 'MODIFICAR';
    }

    //function to convert Temperature $value = in degree celsious, $measureSystem = system to convert
    public static function convertTemp($value, $measureSystem = 'metric') {
        $newValue = $value;
        if ($measureSystem === 'imperial') {
            $newValue = ($value * 9/5) + 32;
        }
        return $newValue;
    }


    //function to convert Wind Speed $value = in km/h, $measureSystem = system to convert
    public static function convertSpeed($value, $measureSystem = 'metric') {
        $newValue = $value;
        if ($measureSystem === 'imperial') {
            $newValue = $value * 0.621371;
        }
        return $newValue;
    }

    //function to convert Rain $value = in mm, $measureSystem = system to convert
    public static function convertLength($value, $measureSystem = 'metric') {
        $newValue = $value;
        if ($measureSystem === 'imperial') {
            $newValue = $value / 25.4;
        }
        return $newValue;
    }

    //function to convert Rain Rate $value = in mm/h, $measureSystem = system to convert
    public static function convertRainfall($value, $measureSystem = 'metric') {
        $newValue = $value;
        if ($measureSystem === 'imperial') {
            $newValue = $value / 25.4;
        }
        return $newValue;
    }

    //function to convert Press $value = in mm/h, $measureSystem = system to convert
    public static function convertPress($value, $measureSystem = 'metric') {
        $newValue = $value;
        if ($measureSystem === 'imperial') {
            $newValue = $value * 0.02953;
        }
        return $newValue;
    }

    //function to convert Press $value = in mm/h, $measureSystem = system to convert
    public static function convertPower($value, $measureSystem = 'metric') {
        $newValue = $value;
        if ($measureSystem === 'imperial') {
            $newValue = $value * 0.092903;
        }
        return $newValue;
    }

    public static function getDegreeUnit($measureSystem) {
        if ($measureSystem === 'metric') {
            return 'C';
        } else {
            return 'F';
        }
    }

    public static function getSpeedUnit($measureSystem) {
        if ($measureSystem === 'metric') {
            return 'km/h';
        } else {
            return 'mph';
        }
    }

    public static function getLengthUnit($measureSystem) {
        if ($measureSystem === 'metric') {
            return 'mm';
        } else {
            return 'in';
        }
    }

    public static function getPressUnit($measureSystem) {
        if ($measureSystem === 'metric') {
            return 'mb';
        } else {
            return 'inHg';
        }
    }

    public static function getRainfallUnit($measureSystem) {
        if ($measureSystem === 'metric') {
            return 'mm/h';
        } else {
            return 'in/h';
        }
    }

    public static function getPowerUnit($measureSystem) {
        if ($measureSystem === 'metric') {
            return 'W/m^2';
        } else {
            return 'W/ft^2';
        }
    }
}
