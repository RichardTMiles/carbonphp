<?php
/**
 * Created by IntelliJ IDEA.
 * User: Miles
 * Date: 7/31/17
 * Time: 6:57 PM
 */

namespace CarbonPHP\interfaces;

/**
 * Interface iTable
 * @package Carbon\Interfaces
 *
 * This should be implemented on all tables in
 * Application/Tables/ folder. Table files should
 * be named exactly that of the database tables. If
 * a tables contains, or may contain, foreign keys
 * then its primary key must be generated with
 *      Carbon\Entities.beginTransaction() : string
 *
 */
interface iRestfulReferences
{
    /**
     * @param array $return
     * @param array $argv
     * @return bool
     */
    public static function Delete(array &$return, array $argv): bool;      // Delete all data from a tables given its primary key

    /**
     * @param array $return
     * @param array $argv - column names desired to be in our array
     * @return bool
     */
    public static function Get(array &$return, array $argv): bool;   // Get tables columns given in argv (usually an array) and place them into our array

    /**
     * @param array $data
     * @return mixed
     */
    public static function Post(array $data) : bool;              // Add and associative array Column => value

    /**
     * @param array $return
     * @param array $argv   - an associative array of Column => Value pairs
     * @return bool  - true on success false on failure
     */
    public static function Put(array &$return, array $argv): bool;
}