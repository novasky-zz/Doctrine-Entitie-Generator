<?php
    /**
     * Carregador automático de classes
     * 
     * @package Simplex_Autoloader
     */
    
    namespace Simplex\Autoloader;
    
    /**
     * Permite o carregamento de classes automaticamente sempre que forem instanciadas. 
     * 
     * @dependecy Para gerenciar multiplos modulos, serÃ¡ necessario a biblioteca ModuleManager
     */
    abstract class Autoloader {
        // @paths registra os caminhos para autoload
        static private $paths = array();
        
        public $current_module = 'default';
        static private $path_module   = '' ,
                       $path_library   = '' ;
        
        
        /**
         * Register the Aplicattion Paths
         * @todo Verificar a necessidade deste modulo
         */
        public static function registerPath($path) {
            if ( ! in_array($path , self::$paths) )
                self::$paths[] = $path;
        }
        
        
        /**
         * Seta o diretorio para bibliotecas terceiras
         * @var string $dir Diretorio
         */
        public static function registerLibraryPath ( $path ) {
            self::$path_library = $path;
        }
        
        
        public static function autoload($class) {
            
            $className = ltrim($class, '\\');
            $fileName  = '';
            $namespace = '';
            if ($lastNsPos = strripos($className, '\\')) {
                $namespace = substr($className, 0, $lastNsPos);
                $className = substr($className, $lastNsPos + 1);
                $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
            
            
            $fileDirectory = self::$path_library . DIRECTORY_SEPARATOR;
            
            /**
             * Busca o arquivo na biblioteca
             */ 
            if ( file_exists( $fileDirectory . $fileName ) ) :
                require_once $fileDirectory . $fileName;
            else :
                die('Não foi possível encontrar o seguinte arquivo: <strong>'.$fileName.'<strong>');
            endif;
            
        }
    }