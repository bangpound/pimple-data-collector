Pimple container data collector
===============================

To enable it, add this dependency to your `composer.json` file:

    "bangpound/pimple-data-collector": "~1.0"

And enable it in your application:

    use Bangpound\Silex\Provider;

    $app->register(new Provider\PimpleDataCollectorProvider(), array(
    ));

The provider depends on `WebProfilerServiceProvider`, so you also need to
enable it if that's not already the case:

    $app->register(new Silex\Provider\WebProfilerServiceProvider());
