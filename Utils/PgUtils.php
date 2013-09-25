<?php
/**
 * Created by PhpStorm.
 * User: prime
 * Date: 24.09.13
 * Time: 16:24
 */

namespace Hq\DaemonsBundle\Utils;


class PgUtils {
    /***********************************\
     *                                   *
     *     HSTORE: PHP => POSTGRESQL     *
     *                                   *
    \***********************************/
    public static function hstoreFromPhp($php_array, $hstore_array = False) {
        if($hstore_array) {
            // Converts a PHP array of Associative Arrays to a PostgreSQL
            // Hstore Array. PostgreSQL Data Type: "hstore[]"
            $pg_hstore = array();
            foreach($php_array as $php_hstore) {
                $pg_hstore[] = self::_hstoreFromPhpHelper($php_hstore);
            }

            // Convert the PHP Array of Hstore Strings to a single
            // PostgreSQL Hstore Array.
            $pg_hstore = self::arrayFromPhp($pg_hstore);
        } else {
            // Converts a single one-dimensional PHP Associative Array
            // to a PostgreSQL Hstore. PostgreSQL Data Type: "hstore"
            $pg_hstore = self::_hstoreFromPhpHelper($php_array);
        }
        return $pg_hstore;
    }

    private static function _hstoreFromPhpHelper(array $php_hstore) {
        $pg_hstore = array();

        foreach ($php_hstore as $key => $val) {
            $search = array('\\', "'", '"');
            $replace = array('\\\\', "''", '\"');

            $key = str_replace($search, $replace, $key);
            $val = $val === NULL
                ? 'NULL'
                : '"' . str_replace($search, $replace, $val) . '"';

            $pg_hstore[] = sprintf('"%s"=>%s', $key, $val);
        }

        return sprintf("%s", implode(',', $pg_hstore));
    }

    /***********************************\
     *                                   *
     *     HSTORE: POSTGRESQL => PHP     *
     *                                   *
    \***********************************/
    public static function hstoreToPhp($string) {
        // If first and last characters are "{" and "}", then we know we're
        // working with an array of Hstores, rather than a single Hstore.
        if(substr($string, 0, 1) == '{' && substr($string, -1, 1) == '}') {
            $array = self::arrayToPhp($string, 'hstore');
            $hstore_array = array();
            foreach($array as $hstore_string) {
                $hstore_array[] = self::_hstoreToPhpHelper($hstore_string);
            }
        } else {
            $hstore_array = self::_hstoreToPhpHelper($string);
        }
        return $hstore_array;
    }

    private static function _hstoreToPhpHelper($string) {
        if(!$string || !preg_match_all('/"(.+)(?<!\\\)"=>(NULL|""|".+(?<!\\\)"),?/U', $string, $match, PREG_SET_ORDER)) {
            return array();
        }
        $array = array();

        foreach ($match as $set) {
            list(, $k, $v) = $set;
            $v = $v === 'NULL'
                ? NULL
                : substr($v, 1, -1);

            $search = array('\"', '\\\\');
            $replace = array('"', '\\');

            $k = str_replace($search, $replace, $k);
            if ($v !== NULL)
                $v = str_replace($search, $replace, $v);

            $array[$k] = $v;
        }
        return $array;
    }

    /**********************************\
     *                                  *
     *     ARRAY: POSTGRESQL => PHP     *
     *                                  *
    \**********************************/
    public static function arrayToPhp($string, $pg_data_type) {
        if(substr($pg_data_type, -2) != '[]') {
            // PostgreSQL arrays are signified by
            $pg_data_type .= '[]';
        }

        $grab_array_values = pg_query("SELECT UNNEST('" . pg_escape_string($string) . "'::" . $pg_data_type . ") AS value");
        $array_values = array();

        $pos = 0;
        while($array_value = pg_fetch_assoc($grab_array_values)) {
            // Account for Null values.
            if(pg_field_is_null($grab_array_values, $pos, 'value')) {
                $array_values[] = Null;
            } else {
                $array_values[] = $array_value['value'];
            }
            $pos++;
        }

        return $array_values;
    }

    /**********************************\
     *                                  *
     *     ARRAY: PHP => POSTGRESQL     *
     *                                  *
    \**********************************/
    public static function arrayFromPhp($array) {
        $return = '';
        foreach($array as $array_value) {
            if($return) {
                $return .= ',';
            }
            $array_value = str_replace("\\", "\\\\", $array_value);
            $array_value = str_replace("\"", "\\\"", $array_value);
            $return .= '"' . $array_value . '"';
        }
        return '{' . $return . '}';
    }
} 