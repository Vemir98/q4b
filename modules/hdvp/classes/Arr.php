<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 06.03.2018
 * Time: 15:56
 */
class Arr extends Kohana_Arr
{
    public function toCamelCase(array $data)
    {
        foreach ($data as $key => $value) {
            $camelCasedKey = Inflector::camelize($key);

            $data[$camelCasedKey] = is_array($value)
                // не много рекурсии
                ? $this->toCamelCase($value)
                : $value;

            if ($camelCasedKey != $key) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}