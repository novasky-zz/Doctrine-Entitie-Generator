<?php
	
	require 'config.php';
    
    
    define ( '__PROJECT' , trim($config['application']['site_folder'] , '/') );
    
    define ( '__PATH' , __DIR__ . DIRECTORY_SEPARATOR );
    
    define ( '__URL' , 'http://' . $_SERVER['SERVER_NAME'] . '/' . __PROJECT . '/' );


    define('__PATH_LIB' , __PATH . "library");
    
    
    /**
     * Registra o AUTOLOADER responsavel por gerenciar o carregamento das classes
     */
    require __PATH_LIB . '/Simplex/Autoloader/Autoloader.php';
    spl_autoload_register(array('Simplex\Autoloader\Autoloader','autoload'));
    
    
    
    /**
     * Seta o diretório da biblioteca vendors
     */
    Simplex\Autoloader\Autoloader::registerLibraryPath( __PATH_LIB );
    
    
    /**
     * Configurações para conexão com o Banco de Dados
     */
    $db = new Simplex\Database\Database( array( 'driver'         => $config['database']['driver']
                                               ,'host'           => $config['database']['host']
                                               ,'user'           => $config['database']['user']
                                               ,'password'       => $config['database']['password']
                                               ,'dbname'         => $config['database']['dbname']
                                               ,'entitiesPath'   => __PATH . 'models' . DIRECTORY_SEPARATOR . 'entities' . DIRECTORY_SEPARATOR
                                               ,'proxiesPath'    => __PATH . 'models' . DIRECTORY_SEPARATOR . 'proxies' . DIRECTORY_SEPARATOR
                                               ,'adapter'        => 'doctrine' ) );
    $em = $db->connect();
    
    //$db->generateEntities();
    

    /**
     * Função genérica para carregar um módulo
     */
    function loadModel ( $model ) {
        require_once __PATH . 'models' . DIRECTORY_SEPARATOR . 'entities' . DIRECTORY_SEPARATOR . $model . '.php';
    }

    $id = 1;

    loadModel('Email');
    $Email = $em->getRepository('\Email')->find($id);

    var_dump($Email);

