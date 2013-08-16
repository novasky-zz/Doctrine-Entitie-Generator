<?php
    /**
     * 
     * 
     * @copyright   Copyright (c) Simplex 
     * @author      William Novasky
     * @package     Simplex_Database
     */
    
    namespace Simplex\Database;
    
    use Doctrine\ORM\Tools\Setup ,
        Doctrine\ORM\EntityManager ,
        Doctrine\ORM\Mapping ,
        Doctrine\Common\ClassLoader ,
        Doctrine\DBAL\DriverManager ,
        Doctrine\ORM\Tools\EntityGenerator,
        Doctrine\ORM\Tools\EntityRepositoryGenerator;
    
    /**
     * Fornece uma camada de configuraÃƒÂ§ÃƒÂ£o do banco de dados a ser usado pela aplicaÃƒÂ§ÃƒÂ£o
     * 
     * @category Simplex
     * @package Simplex_Database
     */
    class Database implements DatabaseInterface {
        
        /**
         * Drivers supported by application
         * @var String $driver
         *      pdo_mysql
         *      pdo_sqlite
         *      pdo_pgsql
         *      pdo_oci
         *      pdo_sqlsrv
         *      oci8
         */
        private $driver;
        
        
        /**
         * Host of the database server
         * @var String $host
         */
        private $host;
        
        
        /**
         * User/Login of the database server
         * @var String $user
         */
        private $user;
        
        
        /**
         * Password of the database server
         * @var String $password
         */
        private $password;
        
        
        /**
         * Port of the database server
         * @var String $port
         */
        private $port;
        
        
        /**
         * Database name
         * @var String $dbname
         */
        private $dbname;
        
        
        /**
         * Model Entities generated
         * @var String $entitiesPath
         */
        private $entitiesPath = '/entities' ;
        
        
        /**
         * Model Proxies generated
         * @var String $proxies
         */
        private $proxiesPath = '/proxies' ;
        
        
        /**
         * Adapter to use on 
         */
        private $adapter = 'pdo';
        
        
        public function __construct ( Array $configs ) {
            $this->adapter      = strtolower($configs['adapter']);
            $this->driver       = $configs['driver'];
            $this->host         = $configs['host'];
            $this->user         = $configs['user'];
            $this->password     = $configs['password'];
            $this->port         = isset($configs['port']) ? $configs['port'] : 3306; // null;
            $this->dbname       = $configs['dbname'];
            $this->charset      = $configs['charset'];
            $this->driverOptions= $configs['driverOptions'];
            $this->entitiesPath = $configs['entitiesPath'];
            $this->proxiesPath  = $configs['proxiesPath'];
            $this->repositoriesPath = $configs['repositoriesPath'];
            
            return $this;
        }
        
        
        
        public function connect () {
            if ( $this->adapter == 'doctrine' )
                return $this->connectDoctrine();
            elseif ( $this->adapter == 'pdo' ) 
                return $this->connectPDO();
        }
        
        
        private function connectPDO () {
            $conn = new \PDO('mysql:host='.$this->host
                                            .';port='.$this->port
                                            .';dbname='.$this->dbname
                                            .';charset='.$this->charset
                            , $this->user
                            , $this->password);
            
            return $conn;
        }
        
        
        private function connectDoctrine () {
            $dir = glob( $pathEntities . '*.php' );
            foreach($dir as &$classFilename) {
                require_once $classFilename;
            }
            
            //Setup::registerAutoloadPEAR(); 
            
            $debug = true;
            $config = Setup::createAnnotationMetadataConfiguration(array($pathEntities), $debug, null, null, false); 
            
            // ConfiguraÃƒÂ§ÃƒÂ£o de acesso ao banco de dados 
            $conn = array(
                'driver'    => $this->driver,
                'host'      => $this->host,
                'user'      => $this->user,
                'password'  => $this->password,
                'dbname'    => $this->dbname,
                'charset'   => $this->charset,
                'driverOptions'=> $this->driverOptions
            ); 
            
            // Obtendo uma instancia do Entity Manager 
            $entityManager = EntityManager::create($conn, $config);
            return $entityManager;
            
            /*
            $teste = new \Teste();
            $teste->setNome('Pafuncio');
            $entityManager->persist($teste);
            $entityManager->flush();
            echo "Cidade criada com o ID ".$teste->getId()."n"; 
            */
        }
        
        private function connectDoctrine2 () {
            $doctrineClassLoader = new ClassLoader('Doctrine' , __PATH . 'vendor\\Doctrine');
            $doctrineClassLoader->register();
            
            $entitiesClassLoader = new ClassLoader('Entities' , $this->entitiesPath);
            $entitiesClassLoader->register();
            
            //Setup::registerAutoloadPEAR(); 
            $configMetadata = Setup::createAnnotationMetadataConfiguration(array($this->entitiesPath), true);
            
            // Configuração de acesso ao banco de dados 
            $config = array( 
                'driver' => $this->driver, 
                'host'  => $this->host,
                'user' => $this->user, 
                'password' => $this->password, 
                'dbname' => $this->dbname
            ); 
            //die( __PATH . 'models' . DIRECTORY_SEPARATOR . 'repositories\\' );
            // Carrega todas as entidades
            
            $dir = glob( $this->entitiesPath . '*.php');
            foreach($dir as &$classFilename) {
                require_once $classFilename;
            }
            
            // Obtendo uma instancia do Entity Manager 
            //$entityManager = EntityManager::create($conn, $config);
            $conn = DriverManager::getConnection($config);
            $entityManager = EntityManager::create($config , $configMetadata);
            /*
            $helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(
                'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($entityManager)
            ));
            */
            
            // execure a query
            $sql = "SELECT * FROM teste";
            $stmt = $conn->query($sql);
            
            // retrieve data
            while ($row = $stmt->fetch()) {
                echo $row['nome'];
            }

            
            
            //$Teste = $entityManager->getRepository('\repositories\Teste');
            //$Teste = new \repositories\Teste();
            $Teste = new \Teste;
            $Teste->setNome('Pafuncio');
            $entityManager->persist($Teste);
            die;
            $entityManager->flush();
            echo '>> ' . $Teste->getId();
            
            //$foo = $Teste->findAll();
            //$teste = \Doctrine\EntityManager\Doctrine::getTable('Teste');
            //$foo = $entityManager->find('Teste' , 1);
            var_dump($foo);
            
            echo '<h3>Conectando com o banco de dados!</h3>';
        }
        
        
        
        public function generateEntities ( $isDevMode = false ) {
            
            if ( $this->adapter != 'doctrine' )
                die('<h3>Esta opção só está disponível para APADTER::DOCTRINE</h3>');
            
            echo '<h3>Doctrine Action</h3>';
            
            $generateWithProxy = true;
            if ( $generateWithProxy ) :
                $config = new \Doctrine\ORM\Configuration();
                $config->setMetadataDriverImpl($config->newDefaultAnnotationDriver( $this->entitiesPath ));
                $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
                $config->setEntityNamespaces(array('Entities'));
                $config->setProxyDir( $this->proxiesPath );
                $config->setProxyNamespace('Proxies');
                $config->setAutoGenerateProxyClasses(true);
            else :
                $config = Setup::createAnnotationMetadataConfiguration( array( $this->entitiesPath ) , $isDevMode);
            endif;
            
            $em = EntityManager::create(
                                        array(
                                            'driver'    => $this->driver
                                           ,'host'      => $this->host
                                           ,'user'      => $this->user
                                           ,'password'  => $this->password
                                           ,'dbname'    => $this->dbname
                                           ,'charset'   => $this->charset
                                           ,'driverOptions'=> $this->driverOptions
                                            )
                                        , $config);
            
            // fetch metadata
            $driver = new \Doctrine\ORM\Mapping\Driver\DatabaseDriver(
              $em->getConnection()->getSchemaManager()
            );
            
            // Habilita o tipo ENUM para as tabelas
            $platform = $em->getConnection()->getDatabasePlatform();
            $platform->registerDoctrineTypeMapping('enum', 'string');
            // #################
            
            $em->getConfiguration()->setMetadataDriverImpl($driver);
            $cmf = new \Doctrine\ORM\Tools\DisconnectedClassMetadataFactory();
            $cmf->setEntityManager($em);
            
            $classes = $driver->getAllClassNames();

            $metadata = array();
            foreach ($classes as &$class) {
                $metadata[] = $cmf->getMetadataFor($class);
            }
            
            //Cria as entidades atravÃƒÂ©s do Metadata Driver Impl setado
            $generator = new EntityGenerator();
            $generator->setRegenerateEntityIfExists(true);
            $generator->setGenerateStubMethods(true);
            $generator->setGenerateAnnotations(true);
            $generator->generate($metadata, $this->entitiesPath );

            //Cria as classes Repository
            foreach ($classes as &$classe) {
                $repository = new EntityRepositoryGenerator();
                $repository->writeEntityRepositoryClass('repositories\\' . $classe, __PATH
                                                       . 'models' . DIRECTORY_SEPARATOR );
            }
            
            //Carrega todas as entities criadas
            $dir = glob( $this->entitiesPath . '*.php' );
            foreach($dir as &$classFilename) {
                require_once $classFilename;
            }

            //Gera as classes Proxies
            $mt = $em->getMetadataFactory();
            $em->getProxyFactory()->generateProxyClasses($mt->getAllMetadata());

            echo '<p class="sucess">Modelos gerados com sucesso</p>';
            die;
            
            
            // or if you prefer yaml or xml
            // $paths = array( __PATH . 'entities' . DIRECTORY_SEPARATOR ); // /path/to/entities-or-mapping-files
            // $config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);
            // $config = Setup::createYAMLMetadataConfiguration($paths, $isDevMode);
        }
        
    }