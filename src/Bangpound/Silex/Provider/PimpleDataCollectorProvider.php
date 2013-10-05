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

use Bangpound\Pimple\DataCollector\PimpleDataCollector;
use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Pimple data collector provider.
 *
 * @author Benjamin Doherty <bjd@bangpound.org>
 */
class PimpleDataCollectorProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['data_collectors'] = array_merge($app['data_collectors'], array(
            'pimple' => $app->share(function ($app) { return new PimpleDataCollector($app); }),
        ));

        $app['data_collector.templates'] = array_merge($app['data_collector.templates'], array(
            array('pimple', '@Pimple/pimple.html.twig')
        ));

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {

            /* @var $loader \Twig_Loader_Filesystem */
            $loader->addPath(realpath(__DIR__ .'/../../../../views/'), 'Pimple');

            return $loader;
        }));
    }

    public function boot(Application $app)
    {
    }
}
