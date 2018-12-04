<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 18.12.2016
 * Time: 9:29
 */
class Text extends Kohana_Text
{
    /**
     * Возвращает массив значения которого являются символы английского алфавита отсортированные по порядку
     * @param bool $uppercase верхний регистр символов
     * @return array
     */
    public static function alphabet($uppercase = true){
        if($uppercase){
            return range('a','z');
        }else{
            return range('A','Z');
        }
    }

    /**
     * Возвращает следующий символ после заданного в аноглийском алфавите
     * @param int|string $char символ английского алфавита или код ASCII символа
     * @return null|string
     */
    public static function alphabetNextChar($char){
        if(strlen($char) !== 1) return null;
        if( ! is_int($char)){
            $char = ord($char);
        }

        if($char < 65 OR ($char > 89 AND $char < 97) OR $char > 121){
            return null;
        }

        return chr(++$char);

    }

    /**
     * Возвращает предыдущий символ после заданного в аноглийском алфавите
     * @param int|string $char символ английского алфавита или код ASCII символа
     * @return null|string
     */
    public static function alphabetPrevChar($char){
        if(strlen($char) !== 1) return null;
        if( ! is_int($char)){
            $char = ord($char);
        }

        if($char < 66 OR ($char > 90 AND $char < 98) OR $char > 122){
            return null;
        }

        return chr(--$char);

    }
}