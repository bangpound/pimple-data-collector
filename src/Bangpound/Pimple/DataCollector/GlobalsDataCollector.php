<?php

namespace Bangpound\Pimple\DataCollector;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\LateDataCollectorInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class GlobalsDataCollector extends DataCollector implements LateDataCollectorInterface, EventSubscriberInterface
{
    /**
     * @var Util\ValueExporter
     */
    private $valueExporter;

    private $filter;

    public function __construct()
    {
        $this->filter = array(
            'GLOBALS' => 'GLOBALS',
            '_GET' => '_GET',
            '_POST' => '_POST',
            '_REQUEST' => '_REQUEST',
            '_COOKIE' => '_COOKIE',
            '_FILES' => '_FILES',
            '_ENV' => '_ENV',
            '_SERVER' => '_SERVER',
        );
        $this->data = array(
            'construct' => array(),
            'request' => array(),
            'collect' => array(),
            'late' => array(),
        );
        foreach (array_diff_key($GLOBALS, $this->filter) as $key => $value) {
            $this->data['construct'][$key] = $this->varToString($value);
        }
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
    public function collect(Request $request = null, Response $response = null, \Exception $exception = null)
    {
        if (is_array($this->filter)) {
            foreach (array_diff_key($GLOBALS, $this->filter) as $key => $value) {
                $this->data['collect'][$key] = $this->varToString($value);
            }
        }
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (is_array($this->filter)) {
            foreach (array_diff_key($GLOBALS, $this->filter) as $key => $value) {
                $this->data['request'][$key] = $this->varToString($value);
            }
        }
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
        return 'globals';
    }

    public function getConstruct()
    {
        return $this->data['construct'];
    }

    public function getRequest()
    {
        return $this->data['request'];
    }

    public function getCollect()
    {
        return $this->data['collect'];
    }

    public function getLate()
    {
        return $this->data['late'];
    }

    /**
     * Collects data as late as possible.
     */
    public function lateCollect()
    {
        if (is_array($this->filter)) {
            foreach (array_diff_key($GLOBALS, $this->filter) as $key => $value) {
                $this->data['late'][$key] = $this->varToString($value);
            }
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

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 1024),
        );
    }
}
