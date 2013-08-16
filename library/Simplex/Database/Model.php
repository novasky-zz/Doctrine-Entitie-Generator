<?php

/**
 * 
 * 
 * @copyright   Copyright (c) Simplex 
 * @author      William Novasky
 * @package     Simplex_Database
 */

namespace Simplex\Database;

/**
 * Implementação de MODEL
 * 
 * @category Simplex
 * @package Simplex_Database
 */
class Model implements ModelInterface {
    
    
    public $DB;
    
    
    public function __construct() {
        $this->db = \Simplex\Registry\Registry::get('DB');
    }
    

    /**
     * Método mágico invocado na chamada de um método
     * Será usado para criar dinamicamente os Getters e Setters
     * 
     * @param $metodo
     * @param $parametros
     * @return atribute value
     */
    public function __call($metodo, $parametros) {
        // se for set*, "seta" um valor para a propriedade
        if (substr($metodo, 0, 3) == 'set') {
            //$var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
            // Retira a palavra 'get' da string
            $metodo = substr($metodo, 3);

            // transforma e substitui o primeiro caractere para minusculo
            $firstChar = strtolower($metodo{0});
            $attribute = $firstChar . substr($metodo, 1);

            $this->$attribute = $parametros[0];
        }
        // se for get*, retorna o valor da propriedade
        elseif (substr($metodo, 0, 3) == 'get') {
            // $var = substr(strtolower(preg_replace('/([a-z])([A-Z])/', "$1_$2", $metodo)), 4);
            // Retira a palavra 'get' da string
            $metodo = substr($metodo, 3);

            // transforma e substitui o primeiro caractere para minusculo
            $firstChar = strtolower($metodo{0});
            $attribute = $firstChar . substr($metodo, 1);
            
            return $this->$attribute;
        }
    }
    
    
    public function setId($cod){
        $this->pk_value = (int)$cod;
        return $this;
    }
    public function getId(){
        return $this->pk_value;
    }
    
    
    public function delete($id) {
        if (is_int($id)) :
            $sql = $this->db->prepare(" DELETE FROM {$this->db_table} WHERE {$this->pk_table} = '$id' ");
            if (!$sql->execute()) :
                throw new \Exception ("Ocorreu um erro ao tentar apagar um registro!");
            endif;
        
        elseif (is_array($id)):
            $sql = $this->db->prepare(" DELETE FROM {$this->db_table} WHERE {$this->pk_table} = :codigo ");
            $this->db->beginTransaction();
            
            foreach ($id as $k) :
                if (! is_int($k) ) :
                    throw new \Exception ("Ocorreu um erro ao tentar apagar um registro!");
                endif;
                
                if (! $sql->execute( array('codigo' => $k) ) ) :
                    throw new \Exception ("Ocorreu um erro ao tentar apagar um registro!");
                endif;
            endforeach;
            $this->db->commit();
        
        else :
            throw new \Exception ("Opção Inválida");
        endif;

        return;
    }
    
    
    public function find($id) {
        $this->pk_value = (int) $id;

        $query = $this->db->prepare(" SELECT * FROM {$this->db_table} WHERE {$this->pk_table} = ? ");
        $query->execute( array($this->pk_value) );
        
        return $query->fetch(\PDO::FETCH_OBJ);
    }
    
    
    public function getColumn($column , $whereColumn , $whereValue) {
        $query = $this->db->prepare(" SELECT {$column} FROM {$this->db_table} WHERE {$whereColumn} = ? ");
        $query->execute( array($whereValue) );
        $data = $query->fetch(\PDO::FETCH_OBJ);
        return $data->$column;
    }
    
    
    public function findAll( $order = null , $order_card = 'ASC' ) {
        $query = $this->db->prepare(" SELECT * FROM {$this->db_table} ".($order ? "ORDER BY {$order} {$order_card}" : '')." ");
        $query->execute();
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function findAllByColumn($whereColumn , $whereValue) {
        $query = $this->db->prepare(" SELECT * FROM {$this->db_table} WHERE {$whereColumn} = ? ");
        $query->execute( array($whereValue) );
        
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function getNextPK() {
        
    }

}