<?php
    
    session_start();
    ob_start();
    
	require_once("config/config.php");
    require_once("vendor/autoload.php");
    
    if (isset($_SESSION['user_id']))
    {
        header("Location: menu.php");
        exit();
    }
    
    $loader = new Twig_Loader_Filesystem('resources/views');
    $twig = new Twig_Environment($loader);
    
    echo $twig->render('index.html.twig');
?>
