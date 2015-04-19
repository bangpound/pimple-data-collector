<?php

/*
 * Pimple Data Collector Provider for Silex.
 *
 * (c) Benjamin Doherty <bjd@bangpound.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bangpound\Silex\Provider;

use Bangpound\Pimple\DataCollector\GlobalsDataCollector;
use Bangpound\Pimple\DataCollector\PimpleDataCollector;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Pimple data collector provider.
 *
 * @author Benjamin Doherty <bjd@bangpound.org>
 */
class PimpleDataCollectorProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app->extend('data_collectors', function ($collectors, Container $c) {
            $collectors['pimple'] = function (Container $c) { return new PimpleDataCollector($c); };
            $collectors['globals'] = function () { return new GlobalsDataCollector(); };

            return $collectors;
        });

        $app['data_collector.templates'] = array_merge($app['data_collector.templates'], array(
            array('pimple', '@Pimple/pimple.html.twig'),
            array('globals', '@Pimple/globals.html.twig'),
        ));

        $app['twig.loader.filesystem'] = $app->extend('twig.loader.filesystem', function ($loader, $app) {

            /* @var $loader \Twig_Loader_Filesystem */
            $loader->addPath(realpath(__DIR__.'/../../../../views/'), 'Pimple');

            return $loader;
        });
    }
}
