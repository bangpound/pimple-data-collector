<?php

namespace Bangpound\Pimple\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;

class PimpleDataCollector extends DataCollector implements LateDataCollectorInterface
{
    /**
     * @var Util\ValueExporter
     */
    private $valueExporter;

    private $container;

    public function __construct(\Pimple $container = null)
    {
        $this->container = $container;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return 'pimple';
    }

    public function getProviders()
    {
        return $this->data['providers'];
    }

    public function getValues()
    {
        return $this->data['values'];
    }

    /**
     * Collects data as late as possible.
     */
    public function lateCollect()
    {
        $this->data = array(
            'providers' => array(),
            'values' => array(),
        );

        $reflector = new \ReflectionObject($this->container);

        $providersReflector = $reflector->getProperty('providers');
        $providersReflector->setAccessible(true);
        $providers = $providersReflector->getValue($this->container);
        foreach ($providers as $provider) {
            $this->data['providers'][] = get_class($provider);
        }

        $valuesReflector = $reflector->getProperty('values');
        $valuesReflector->setAccessible(true);
        $values = $valuesReflector->getValue($this->container);
        foreach ($this->container->keys() as $key) {
            $this->data['values'][$key] = $this->varToString($values[$key]);
        }
    }

    /**
     * Converts a PHP variable to a string.
     *
     * @param mixed $var A PHP variable
     *
     * @return string The string representation of the variable
     */
    protected function varToString($var)
    {
        if (null === $this->valueExporter) {
            $this->valueExporter = new Util\ValueExporter();
        }

        return $this->valueExporter->exportValue($var);
    }
}
