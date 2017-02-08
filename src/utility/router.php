<?php

namespace App\Utility;

class Router {

    private $url; // Contiendra l'URL sur laquelle on souhaite se rendre
    private $routes = []; // Contiendra la liste des routes

    public function __construct($url){
        $this->url = $url;
    }
    
    public function get($path, $callable){
        $route = new Route(urldecode($path), $callable);
        $this->routes["GET"][] = $route;
        return $route; // On retourne la route pour "enchainer" les méthodes
    }
    
    public function post($path, $callable){
        $route = new Route(urldecode($path), $callable);
        $this->routes["POST"][] = $route;
        return $route;
    }
    
    public function run(){
        if(!isset($this->routes[$_SERVER['REQUEST_METHOD']])){
            throw new \Exception('REQUEST_METHOD ' . Translator::_('does not exist'));
        }
        foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){
            if($route->match($this->url)){
                return $route->call();
            }
        }
        throw new \Exception(Translator::_('unknown page'));
    }
}