<?php
declare(strict_types=1);
/**
 * Math class
 *
 * @package   YetiForcePDF
 *
 * @copyright YetiForce Sp. z o.o
 * @license   MIT
 * @author    Rafal Pospiech <r.pospiech@yetiforce.com>
 */

namespace YetiForcePDF;

/**
 * Class Math
 */
class Math
{
    public static $scale = 4;

    /**
     * Add two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function add(...$numbers)
    {
        $numbers = array_reverse($numbers);
        $result = array_pop($numbers);
        $numbers = array_reverse($numbers);
        foreach ($numbers as $number) {
            $result = bcadd($result, $number, static::$scale);
        }
        return $result;
    }

    /**
     * Subtract two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function sub(...$numbers)
    {
        $numbers = array_reverse($numbers);
        $result = array_pop($numbers);
        $numbers = array_reverse($numbers);
        foreach ($numbers as $number) {
            $result = bcsub($result, $number, static::$scale);
        }
        return $result;
    }

    /**
     * Multiply two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function mul(...$numbers)
    {
        $numbers = array_reverse($numbers);
        $result = array_pop($numbers);
        $numbers = array_reverse($numbers);
        foreach ($numbers as $number) {
            $result = bcmul($result, $number, static::$scale);
        }
        return $result;
    }

    /**
     * Divide two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function div(...$numbers)
    {
        $numbers = array_reverse($numbers);
        $result = array_pop($numbers);
        $numbers = array_reverse($numbers);
        foreach ($numbers as $number) {
            $result = bcdiv($result, $number, static::$scale);
        }
        return $result;
    }

    /**
     * Compare two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function comp(string $left, string $right)
    {
        return bccomp($left, $right, static::$scale);
    }
}
