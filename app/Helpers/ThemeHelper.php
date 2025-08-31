<?php

namespace App\Helpers;

class ThemeHelper
{
    public static function getColors()
    {
        return config('pawtel.theme.colors');
    }

    public static function getPrimaryColor()
    {
        return config('pawtel.theme.colors.primary');
    }

    public static function getSecondaryColor()
    {
        return config('pawtel.theme.colors.secondary');
    }

    public static function generatePawPrintSvg($color = null)
    {
        $color = $color ?: self::getPrimaryColor();

        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='30' r='20' fill='{$color}'/%3E%3Ccircle cx='30' cy='70' r='15' fill='{$color}'/%3E%3Ccircle cx='70' cy='70' r='15' fill='{$color}'/%3E%3Ccircle cx='50' cy='60' r='15' fill='{$color}'/%3E%3C/svg%3E";
    }
}
