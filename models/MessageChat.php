<?php
    class MessageChat {
        public $Id;
        public $idUserSend;
        public $idUserRecive;
        public $message;
        public $imageURL;
        public $time_send;
        public $response; //0 envio, 1 llego

        public function __construct($params) {
            $this->Id = (array_key_exists('Id',$params)) ? $params['Id'] : -1;
            $this->idUserSend = (array_key_exists('idUserSend',$params)) ? $params['idUserSend'] : -1;
            $this->idUserRecive = (array_key_exists('idUserRecive',$params)) ? $params['idUserRecive'] : -1;
            $this->message = (array_key_exists('message',$params)) ? $params['message'] : "";
            $this->imageURL = (array_key_exists('imageURL',$params)) ? $params['imageURL'] : "";
            $this->time_send = (array_key_exists('time_send',$params)) ? $params['time_send'] : "";
            $this->response = (array_key_exists('response',$params)) ? $params['response'] : 0;
        }
        public function toJSON(){
            return get_object_vars($this);
        }

    }
?>