<?php

namespace DiagramGenerator;

use DiagramGenerator\GeneratorConfig as Config;
use Symfony\Component\Routing\Router;

/**
 * Class responsible for generating dynamic diagram urls
 * @author Alex Kovalevych <alexkovalevych@gmail.com>
 */
class UrlHelper
{
    /**
     * @var \Symfony\Component\Router\Router
     */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router  = $router;
    }

    /**
     * Returns unsecure scheme url
     * @param  Config $config  Diagram config
     * @param  string $routing Routing name, diagram will be accessible via
     * @return string
     */
    public function getNonSecureUrl(Config $config, $routing)
    {
        return $this->generateRoutingByScheme($routing, $config->toArray(), false);
    }

    /**
     * Returns secure scheme url
     * @param  Config $config  Diagram config
     * @param  string $routing Routing name, diagram will be accessible via
     * @return string
     */
    public function getSecureUrl(Config $config, $routing)
    {
        return $this->generateRoutingByScheme($routing, $config->toArray(), true);
    }

    /**
     * Method to generate routing by the given scheme
     * @param  string  $routing    Routing name
     * @param  array   $parameters Routing parameters
     * @param  boolean $secure     If true, https scheme will be used, http otherwise
     * @return string
     */
    protected function generateRoutingByScheme($routing, array $parameters, $secure)
    {
        $currentScheme = $this->router->getContext()->getScheme();

        if ($secure) {
            $this->router->getContext()->setScheme('https');
        } else {
            $this->router->getContext()->setScheme('http');
        }

        $url = $this->router->generate($routing, $parameters, true);
        $this->router->getContext()->setScheme($currentScheme);

        return $url;
    }
}
