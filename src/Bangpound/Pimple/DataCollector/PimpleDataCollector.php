<?php

namespace Bangpound\Pimple\DataCollector;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class PimpleDataCollector extends DataCollector {

    private $values;
    private $providers;

    public function __construct(\Pimple $container = null)
    {
        $reflector = new \ReflectionObject($container);

        $providersReflector = $reflector->getProperty('providers');
        $providersReflector->setAccessible(true);
        $providers = $providersReflector->getValue($container);
        foreach ($providers as $provider) {
            $this->providers[] = get_class($provider);
        }

        $valuesReflector = $reflector->getProperty('values');
        $valuesReflector->setAccessible(true);
        $values = $valuesReflector->getValue($container);
        foreach ($values as $key => $value) {
            $this->values[$key] = $this->varToString($value);
        }
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request   A Request instance
     * @param Response $response  A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'providers' => $this->providers,
            'values' => $this->values,
        );
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
}
