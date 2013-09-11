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
use Symfony\Bridge\Twig\Extension\CodeExtension;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Bundle\WebProfilerBundle\Controller\ProfilerController;

/**
 * Pimple data collector provider.
 *
 * @author Benjamin Doherty <bjd@bangpound.org>
 */
class PimpleDataCollectorProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['profiler']->add(new PimpleDataCollector($app));

        $app['web_profiler.controller.profiler'] = $app->share(function ($app) {
            $templates = $app['data_collector.templates'];
            $templates[] = array('pimple', 'pimple.html.twig');
            return new ProfilerController($app['url_generator'], $app['profiler'], $app['twig'], $templates, $app['web_profiler.debug_toolbar.position']);
        });

        $app['twig.loader.filesystem'] = $app->share($app->extend('twig.loader.filesystem', function ($loader, $app) {
            /* @var $loader \Twig_Loader_Filesystem */
            $loader->addPath(realpath(__DIR__ .'/../../../../views/'));
            return $loader;
        }));


    }

    public function boot(Application $app)
    {
    }
}
