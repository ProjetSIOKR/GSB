<?php
declare(strict_types=1);
namespace Outils;

abstract class MyTwig {

    private static function getLoader() {

        $loader = new \Twig\Loader\FilesystemLoader(PATH_VIEWS);
        $environmentTwig= new \Twig\Environment($loader, [
            'cache' => false,
            'debug' => true,
        ]);
        $environmentTwig->addExtension(new \Twig\Extension\DebugExtension());
        return $environmentTwig;
        
    }

    public static function afficheVue($vue, $params=[]) {
        $twig = self::getLoader();
        $template = $twig->load($vue);
        echo $template->render($params);
    }
}
