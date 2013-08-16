<?php
    /**
     * Configs Application
     */
    
    
    header("Content-Type: text/html; charset=UTF-8",true);
    
    setlocale(LC_ALL, 'BRA');
    
    date_default_timezone_set('America/Sao_Paulo');
    
    
    /**
     * Servidores de acesso local (usa configurações diferentes p/ acesso)
     */
    $cfg_servers = array('10.0.0.1', '127.0.0.1', 'localhost', 'servidor');
    
    
    /**
     * Define se está acessando um servidor local ou não
     */
    define('_IN_LOCALHOST', in_array($_SERVER['SERVER_NAME'], $cfg_servers));
    
    
    
    /**
     * @LOCALHOST - DESENVOLVIMENTO
     */
    if (_IN_LOCALHOST) :
        /**
         * Configuracoes do projeto
         */
        $config['application'] = array( 'site_folder'   => 'doctrine'
                                       ,'site_titulo'   => 'Exemplo Doctrine');
        
        
        /**
         * Configuracoes de conexao com banco de dados
         */
        $config['database'] = array( 'driver'         => 'pdo_mysql'
                                    ,'host'           => 'localhost'
                                    ,'user'           => 'root'
                                    ,'password'       => ''
                                    ,'dbname'         => 'meu_db' );
        
        
        /**
         * Configuracoes de e-mail
         */
        $config['email'] = array( 'use_smtp'    => true
                                 ,'smtp_auth'   => true
                                 ,'smtp_secure' => ''
                                 ,'host'        => ''
                                 ,'user'        => ''
                                 ,'pass'        => ''
                                 ,'port'        => 587
                                 
                                 ,'from_name'   => ''
                                 ,'from_email'  => '' );
        
        
    /**
     * @WEB - PRODUCAO
     */
    else :
        /**
         * Configuracoes do projeto
         */
        $config['application'] = array( 'site_folder' => '' );
        
        
        /**
         * Configuracoes de conexao com banco de dados
         */
        $config['database'] = array( 'driver'         => 'pdo_mysql'
                                    ,'host'           => 'localhost'
                                    ,'user'           => 'root'
                                    ,'password'       => ''
                                    ,'dbname'         => 'db_name' );
        
        
        /**
         * Configuracoes de e-mail
         */
        $config['email'] = array( 'use_smtp'    => true
                                 ,'smtp_auth'   => false
                                 ,'smtp_secure' => ''
                                 ,'host'        => ''
                                 ,'user'        => ''
                                 ,'pass'        => ''
                                 ,'port'        => 25
                                 
                                 ,'from_name'   => ''
                                 ,'from_email'  => '' );
    endif;