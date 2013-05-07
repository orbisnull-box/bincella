<?php

namespace BinCella;

class F
{
    public static function arrayMergeRecursive()
    {
        if (func_num_args() === 0) {
            throw new \BadMethodCallException(__METHOD__ . ' mast be used with one or more params');
        }

        if (func_num_args() === 1) {
            return func_get_args()[0];
        }
        $arrays = func_get_args();
        $merged = array();
        while ($arrays) {
            $array = array_shift($arrays);
            if (!is_array($array)) {
                trigger_error(__METHOD__ . ' encountered a non array argument', E_USER_WARNING);
                return;
            }
            if (!$array)
                continue;
            foreach ($array as $key => $value)
                if (is_string($key))
                    if (is_array($value) && array_key_exists($key, $merged) && is_array($merged[$key]))
                        $merged[$key] = self::merge_recursive($merged[$key], $value);
                    else
                        $merged[$key] = $value;
                else
                    $merged[] = $value;
        }
        return $merged;
    }

    public static function classNameFull($class, $namespacePart = null)
    {
        if (strpos($class, '\\') === false) {
            if (strpos($class, __NAMESPACE__) === false) {
                $parts[] = self::rootNamespace();
            }
            if (!empty($namespacePart)) {
                $parts[] = $namespacePart;
            }
            $parts[] = $class;
            $class = '\\' . implode('\\', $parts);
        }
        return class_exists($class, true) ? $class : null;
    }

    public static function classNameShort($class)
    {
        $pos = strrpos($class, '\\');
        if ($pos !== false) {
            $class = substr($class, $pos+1);
        }
        return $class;
    }
}