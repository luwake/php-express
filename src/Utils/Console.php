<?php
namespace Luwake\Utils;

class Console
{

    static public function log($str, ...$arr)
    {
        if (is_array($str)) {
            $str = json_encode($str);
        }
        $str = sprintf($str, $arr);
        echo "<script>console.log(\"$str\");</script>";
    }

    static public function debug($str, ...$arr)
    {
        if (is_array($str)) {
            $str = json_encode($str);
        }
        $str = sprintf($str, $arr);
        echo "<script>console.debug(\"$str\");</script>";
    }

    static public function info($str, ...$arr)
    {
        if (is_array($str)) {
            $str = json_encode($str);
        }
        $str = sprintf($str, $arr);
        echo "<script>console.info(\"$str\");</script>";
    }

    static public function warn($str, ...$arr)
    {
        if (is_array($str)) {
            $str = json_encode($str);
        }
        $str = sprintf($str, $arr);
        echo "<script>console.warn(\"$str\");</script>";
    }

    static public function error($str, ...$arr)
    {
        if (is_array($str)) {
            $str = json_encode($str);
        }
        $str = sprintf($str, $arr);
        echo "<script>console.error(\"$str\");</script>";
    }

    static public function assert($str, ...$arr)
    {}

    static public function clear()
    {}

    static public function dir($node)
    {}

    static public function dirxml($node)
    {}

    static public function trace($str, ...$arr)
    {}

    static public function group($str, ...$arr)
    {}
}