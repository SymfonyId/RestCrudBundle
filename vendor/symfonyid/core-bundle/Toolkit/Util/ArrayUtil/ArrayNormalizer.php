<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\Util\ArrayUtil;

use Symfonian\Indonesia\CoreBundle\Toolkit\Util\StringUtil\CamelCasizer;

final class ArrayNormalizer
{
    /**
     * @param array $data
     * @param mixed $object
     * @return mixed $object
     */
    public static function convertToObject(array $data, $object)
    {
        if (!is_object($object)) {
            return;
        }

        foreach ($data as $key => $value) {
            $method = CamelCasizer::underScoretToCamelCase(sprintf('set_%s', $key));

            if (method_exists($object, $method)) {
                call_user_func_array(array($object, $method), array($value));
            } else {
                $method = CamelCasizer::underScoretToCamelCase($key);

                if (!method_exists($object, $method)) {
                    $method = CamelCasizer::underScoretToCamelCase(sprintf('is_%s', $key));
                }

                call_user_func_array(array($object, $method), array($value));
            }
        }

        return $object;
    }

    public static function convertToArray($object)
    {

    }
}