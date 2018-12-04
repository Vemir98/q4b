<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 10.10.2016
 * Time: 18:09
 *
 * Класс служит для реализации перечислений которых нет в PHP без использования splEnums
 */
abstract class Enum
{
    /**
     * Enum value
     *
     * @var mixed
     */
    protected $value;
    
    /**
     * Статический кеш хранения существующих констант объекта
     *
     * @var array
     */
    protected static $cache = array();

    /**
     * 
     * @param mixed $value
     *
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    public function __construct($value)
    {
        if (!$this->isValid($value)) {
            throw new \UnexpectedValueException("Value '$value' is not part of the enum " . get_called_class());
        }
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Возвращает ключь перечисления
     *
     * @return mixed
     */
    public function getKey()
    {
        return static::search($this->value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * Сравнивает перечисление
     *
     * @return bool True if Enums are equal, false if not equal
     */
    final public function equals(Enum $enum)
    {
        return $this->getValue() === $enum->getValue();
    }

    /**
     * Возвращает все ключи в перечислении
     *
     * @return array
     */
    public static function keys()
    {
        return array_keys(static::toArray());
    }

    /**
     * Возвращает экземпляры класса Enum для каждой константы
     * @return static[] Constant name in key, Enum instance in value
     */
    public static function values()
    {
        $values = array();
        foreach (static::toArray() as $key => $value) {
            $values[$key] = new static($value);
        }
        return $values;
    }

    /**
     * Возвращает Перечисление в виде массива
     * @return array Constant name in key, constant value in value
     */
    public static function toArray()
    {
        $class = get_called_class();
        if (!array_key_exists($class, static::$cache)) {
            $reflection = new \ReflectionClass($class);
            static::$cache[$class] = $reflection->getConstants();
        }
        return static::$cache[$class];
    }

    /**
     * Валидация значения перечисления
     * @param $value
     * @return bool
     */
    public static function isValid($value)
    {
        return in_array($value, static::toArray(), true);
    }

    /**
     * Валидация ключа перечисления
     * @param $key
     * @return bool
     */
    public static function isValidKey($key)
    {
        $array = static::toArray();
        return isset($array[$key]);
    }

    /**
     * Поиск ключа по значению
     * @param $value
     * @return mixed
     */
    public static function search($value)
    {
        return array_search($value, static::toArray(), true);
    }

    /**
     * Returns a value when called statically like so: MyEnum::SOME_VALUE() given SOME_VALUE is a class constant
     *
     * @param string $name
     * @param array $arguments
     *
     * @return static
     * @throws \BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name])) {
            return new static($array[$name]);
        }
        throw new \BadMethodCallException("No static method or enum constant '$name' in class " . get_called_class());
    }
}