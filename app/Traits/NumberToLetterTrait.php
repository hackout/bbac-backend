<?php
namespace App\Traits;

use Exception;

/**
 * 数字转字母
 *
 * @author Dennis Lui <hackout@vip.qq.com>
 */
trait NumberToLetterTrait
{
    public function toLetter(float|string $number): string
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

}