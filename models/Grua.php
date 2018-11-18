<?php
    class Grua {
        public $Id;
        public $Name;
        public $Description;
        public $imageURL;

        public function __construct($params) {
            $this->Id = (array_key_exists('Id',$params)) ? $params['Id'] : -1;
            $this->Name = (array_key_exists('Name',$params)) ? $params['Name'] : "";
            $this->Description = (array_key_exists('Description',$params)) ? $params['Description'] : "";
            $this->imageURL = (array_key_exists('imageURL',$params)) ? $params['imageURL'] : "";
        }
        public function toJSON(){
            return get_object_vars($this);
        }
    };
?>