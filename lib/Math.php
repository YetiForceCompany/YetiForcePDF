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
    public static function add(string $left, string $right)
    {
        return bcadd($left, $right, static::$scale);
    }

    /**
     * Subtract two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function sub(string $left, string $right)
    {
        return bcsub($left, $right, static::$scale);
    }

    /**
     * Multiply two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function mul(string $left, string $right)
    {
        return bcmul($left, $right, static::$scale);
    }

    /**
     * Divide two numbers
     * @param string $left
     * @param string $right
     * @return string
     */
    public static function div(string $left, string $right)
    {
        return bcdiv($left, $right, static::$scale);
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
