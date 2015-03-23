<?php

namespace simpleserv\webfilesframework;

class  MItem {

    protected $m_iId = 0;

    public static $m__sClassName;

    public function __constructItem() {

        //sets the registryId on a uniqueValue.
        //$this->m_sRegistryId = md5(uniqid(rand()));


        //$this->db = $__class_array['class_db'];
        //$this->right = $__class_array['class_right'];
        
    }

    

    public function setId($itemId) {
    	$this->m_iId = $itemId;
    }

    public function getId() {
        return $this->m_iId;
    }


    /**
     * Returns the registry Id of the actual object.
     * @return registryId of object.
    
    public function getRegistryId() {
        return $this->m_sRegistryId;
    } */

}