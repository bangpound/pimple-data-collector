<?php

namespace Bangpound\Pimple\DataCollector\Util;

class ValueExporter extends \Symfony\Component\HttpKernel\DataCollector\Util\ValueExporter
{

    public function exportValue($value)
    {
        if (is_a($value, 'Closure')) {
            $reflector = new \ReflectionObject($value);

            return (string) $reflector->getMethod('__invoke');
        }

        return parent::exportValue($value);
    }
}
