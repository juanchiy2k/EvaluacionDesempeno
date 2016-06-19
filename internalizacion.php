<?php
        // Idioma
        //$lang = 'es_ES';

//if(!isset($_COOKIE['lang'])){
    if(isset($_GET) && !empty($_GET) && isset($_GET['lang'])){
            $_COOKIE['lang'] = $_GET['lang'] === 'en' ? 'en' : 'es';
    }elseif (!isset($_COOKIE['lang'])) {
        $_COOKIE['lang'] = 'es';
    }
//}
    setcookie('lang', $_COOKIE['lang']);
    




if ( $_COOKIE['lang'] == "en") {
   $lang = 'en_EN';
} else {
   $lang = 'es_ES';
} 

        // Dominio
        $text_domain = 'multilingual_twig';
        
        // putenv/setlocale configurarán tu idioma.
        putenv('LC_ALL='.$lang);
        setlocale(LC_ALL, $lang);
        
        // La ruta a los archivos de traducción
        bindtextdomain($text_domain, './locale' );
        
        // El codeset del textdomain
        bind_textdomain_codeset($text_domain, 'UTF-8'); 
        
        // El Textdomain
        textdomain($text_domain);
        
//        if(!empty($_SESSION)){
//            // Incluimos el template engine
//            include('includes/templateEngine.inc.php');
//
//            // Cargar extensión twig para poder usar gettext()
//            $twig->addExtension(new Twig_Extensions_Extension_I18n());
//
//         }
        
    
    
    
    

?>
