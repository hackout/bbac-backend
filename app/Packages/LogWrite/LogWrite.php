<?php
namespace App\Packages\LogWrite;


class LogWrite
{
    private $maps;

    public function __construct()
    {
        $this->maps = RouteMaps::getMaps();
    }

    public function convert(string $method,string $route_name)
    {
        $list = $this->maps[strtoupper($method)];
        if(!array_key_exists($route_name,$list)) return $route_name;
        return $list[$route_name];
    }
}