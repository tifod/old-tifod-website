<?php
namespace App\Utility;

class Twig {
    public static function render ($template,array $params = ['nothing']){
        \Twig_Autoloader::register();

        $loader = new \Twig_Loader_Filesystem( __DIR__ . '/../views');
        $twig = new \Twig_Environment($loader,['cache' => __DIR__ . '/twig_cache','auto_reload' => true]);

        // add current user
        $twig->addGlobal('current_user', \App\User\Entity::getCurrentUser());
        
        // add language_list, current_language_code
        $twig->addGlobal('language_list', Translator::getAllLanguagesCode());
        $twig->addGlobal('current_language_code', Translator::getCurrentLanguage());
        
        // add error_message
        if (!empty($_SESSION['error_message'])){
            $twig->addGlobal('error_message', $_SESSION['error_message']);
            unset($_SESSION['error_message']);
        }

        // previous page
        $twig->addGlobal('previousPage', (!empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : null));

        // timeago filter
        $filter = new \Twig_SimpleFilter('timeago', function ($datetime) {
            $time = time() - strtotime($datetime);
            $units = array (
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
            );
            foreach ($units as $unit => $val) {
                if ($time < $unit) continue;
                $numberOfUnits = floor($time / $unit);
                return ($val == 'second')? 'a few seconds ago' : (($numberOfUnits>1) ? $numberOfUnits : 'a') .' '.$val.(($numberOfUnits>1) ? 's' : '').' ago';
            }
        });
        $twig->addFilter($filter);

        // Markdown filter
        $mdFilter = new \Twig_SimpleFilter('markdown', function ($text) {
            $parsedown = new Parsedown();
            $parsedown->setMarkupEscaped(true)->setBreaksEnabled(true);
            return $parsedown->text($text);
        }, ['is_safe' => ['html']]);
        $twig->addFilter($mdFilter);
        
        // Translation function
        $translation_function = new \Twig_SimpleFunction('_', function ($str) { return Translator::_($str); });
        $twig->addFunction($translation_function);

        if ($params == ['nothing']){
            $output = $twig->render($template);
        } else {
            $output = $twig->render($template, $params);
        }
        return $output;
    }
}