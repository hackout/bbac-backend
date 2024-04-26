<?php

namespace App\Packages\Excel;
use Exception;

class ExcelPlus{

    /**
     * 数字转Excel列
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer|float|string $number
     * @return string
     */
    public function covertToLetter(int|float|string $number):string
    {
        return self::numberToLetter($number);
    }

    /**
     * 数字转Excel列
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  integer|float|string $number
     * @return string
     */
    public static function numberToLetter(int|float|string $number):string
    {
        $letters = range('A','Z');
        $num = count($letters);
        $maxLength = pow($num,3);
        if($number > $maxLength)
        {
            throw new Exception("最大值不超过$maxLength");
        }
        $strings = [
            intval($number / $num) > $num ? intval(intval($number / $num) / $num) : 0,
            $number > $num ? intval($number / $num) % $num : 0,
            $number % $num
        ];
        $result = '';
        foreach($strings as $key)
        {
            if($key)
            {
                $result.= $letters[$key-1];
            }
        }
        return $result;
    }

    /**
     * hash颜色转Excel颜色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $string
     * @return string
     */
    public function convertToColor(string $string):string
    {
        return self::hashToExcelColor($string);
    }

    /**
     * hash颜色转Excel颜色
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  string $string
     * @return string
     */
    public static function hashToExcelColor(string $string):string
    {
        return strtoupper(str_replace('#','',$string));
    }

    /**
     * pt转pt
     *
     * @author Dennis Lui <hackout@vip.qq.com>
     * @param  float $px
     * @return float
     */
    public static function pxToPt($px) {
        return $px * (72 / 96);
    }
    
}