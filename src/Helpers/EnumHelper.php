<?php

namespace ZnCore\Enum\Helpers;

use InvalidArgumentException;
use ZnCore\Arr\Helpers\ArrayHelper;
use ZnCore\Code\Helpers\ReflectionHelper;
use ZnCore\Enum\Interfaces\GetLabelsInterface;
use ZnCore\Instance\Exceptions\NotInstanceOfException;
use ZnCore\Instance\Helpers\ClassHelper;
use ZnCore\Text\Helpers\Inflector;

/**
 * Работа с перечислениями
 */
class EnumHelper
{

    /**
     * Получить все значения констант
     * @param string $className
     * @param null $prefix
     * @return array
     */
    public static function getValues(string $className, $prefix = null): array
    {
        $constants = static::all($className, $prefix);
        $constants = array_values($constants);
        $constants = array_unique($constants);
        return $constants;
    }

    /**
     * Валидация значения
     *
     * Проверяется наличие значения в списке констант.
     * При ошибке вызывает исключение.
     * @param string $className
     * @param $value
     * @param null $prefix
     * @throws InvalidArgumentException
     */
    public static function validate(string $className, $value, $prefix = null): void
    {
        if (!self::isValid($className, $value, $prefix)) {
            $class = static::class;
            throw new InvalidArgumentException("Value \"$value\" not contains in \"$class\" enum");
        }
    }

    /**
     * Проверяет, является ли значение валидным
     * @param string $className
     * @param $value
     * @param null $prefix
     * @return bool
     */
    public static function isValid(string $className, $value, $prefix = null): bool
    {
        return in_array($value, static::getValues($className, $prefix));
    }

    /*public static function getValue(string $className, $value, $default = null, $prefix = null)
    {
        if (self::isValid($className, $value, $prefix)) {
            return $value;
        } else {
            if ($default !== null && self::isValid($className, $default, $prefix)) {
                return $default;
            }
            $values = self::getValues($className, $prefix);
            return $values[0];
        }
    }*/

    /**
     * Получить надпись константы из значения
     * @param string $className
     * @param $constValue
     * @return string
     */
    public static function getLabel(string $className, $constValue): string
    {
        $labels = self::getLabels($className);
        return $labels[$constValue];
    }

    /**
     * Получить надписи все констант
     * @param string $className
     * @return array
     * @throws NotInstanceOfException
     * @throws \ReflectionException
     */
    public static function getLabels(string $className): array
    {
        /** @var GetLabelsInterface $className */
        ClassHelper::checkInstanceOf($className, GetLabelsInterface::class, true);
        $labels = $className::getLabels();
        return $labels;
    }

    /**
     * Получить список все констант в виде массива (id => title)
     * @param string $className
     * @return array
     */
    public static function getOptions(string $className): array
    {
        $items = self::getItems($className);
        return ArrayHelper::map($items, 'id', 'title');
    }

    /**
     * Получить список все констант в виде массива записей
     * @param string $className
     * @return array
     * @throws \ReflectionException
     */
    public static function getItems(string $className): array
    {
        $all = EnumHelper::all($className);
        $labels = self::getLabelsForce($className, $all);
        $items = [];
        foreach ($all as $name => $id) {
            $items[] = [
                'id' => $id,
                'name' => mb_strtolower($name),
                'title' => $labels[$id],
            ];
        }
        return $items;
    }

    /**
     * Получить рефлексию всех констант
     * @param string $className
     * @param string|null $prefix
     * @return array
     */
    protected static function all(string $className, ?string $prefix = null): array
    {
        if (!empty($prefix)) {
            $constants = ReflectionHelper::getConstantsByPrefix($className, $prefix);
        } else {
            $constants = ReflectionHelper::getConstants($className);
        }
        return $constants;
    }

    protected static function getLabelsForce(string $className, array $all = null): array
    {
        try {
            $labels = EnumHelper::getLabels($className);
        } catch (NotInstanceOfException $e) {
            $labels = null;
        }
        if (empty($labels)) {
            $all = $all ?: EnumHelper::all($className);
            $labels = array_flip($all);
            $labels = array_map(function ($value) {
                $value = strtolower($value);
                return Inflector::titleize($value);
            }, $labels);
        }
        return $labels;
    }
}
