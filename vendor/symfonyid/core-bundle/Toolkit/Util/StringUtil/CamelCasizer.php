<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\Util\StringUtil;

final class CamelCasizer
{
    /**
     * @param $string
     * @return string
     */
    public static function underScoretToCamelCase($string)
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }

    /**
     * @param $string
     * @return string
     */
    public static function camelCaseToUnderScore($string)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $string));
    }
}
