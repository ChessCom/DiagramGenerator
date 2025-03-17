<?php

namespace DiagramGenerator;

use JMS\Serializer\Serializer;
use Symfony\Component\Routing\Router;

/**
 * Class responsible for generating dynamic diagram urls.
 */
class UrlHelper
{
    /**
     * @var \Symfony\Component\Router\Router
     */
    protected $router;

    public function __construct(Router $router, Serializer $serializer)
    {
        $this->router = $router;
        $this->serializer = $serializer;
    }

    /**
     * Returns unsecure scheme url.
     *
     * @param Config $config  Diagram config
     * @param string $routing Routing name, diagram will be accessible via
     *
     * @return string
     */
    public function getNonSecureUrl(Config $config, $routing)
    {
        return $this->generateRoutingByScheme($routing, $this->convertConfigToParameters($config), false);
    }

    /**
     * Returns secure scheme url.
     *
     * @param Config $config  Diagram config
     * @param string $routing Routing name, diagram will be accessible via
     *
     * @return string
     */
    public function getSecureUrl(Config $config, $routing)
    {
        return $this->generateRoutingByScheme($routing, $this->convertConfigToParameters($config), true);
    }

    /**
     * Method to generate routing by the given scheme.
     *
     * @param string $routing    Routing name
     * @param array  $parameters Routing parameters
     * @param bool   $secure     If true, https scheme will be used, http otherwise
     *
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

    /**
     * Takes config and converts it to associative array.
     *
     *
     * @return array
     */
    protected function convertConfigToParameters(Config $config)
    {
        return json_decode($this->serializer->serialize($config, 'json'), true);
    }
}
