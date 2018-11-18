<?php
    class Calificacion {
        public $idUser;
        public $idGrua;
        public $vote_5;
        public $vote_4;
        public $vote_3;
        public $vote_2;
        public $vote_1;
        public $message;
        public $imageURL;

        public function __construct($params) {
            $this->idUser = (array_key_exists('idUser',$params)) ? $params['idUser'] : -1;
            $this->idGrua = (array_key_exists('idGrua',$params)) ? $params['idGrua'] : -1;
            $this->vote_5 = (array_key_exists('vote_5',$params)) ? $params['vote_5'] : 0;
            $this->vote_4 = (array_key_exists('vote_4',$params)) ? $params['vote_4'] : 0;
            $this->vote_3 = (array_key_exists('vote_3',$params)) ? $params['vote_3'] : 0;
            $this->vote_2 = (array_key_exists('vote_2',$params)) ? $params['vote_2'] : 0;
            $this->vote_1 = (array_key_exists('vote_1',$params)) ? $params['vote_1'] : 0;
            $this->message = (array_key_exists('message',$params)) ? $params['message'] : "";
            $this->imageURL = (array_key_exists('imageURL',$params)) ? $params['imageURL'] : "";
        }
        public function toJSON(){
            return get_object_vars($this);
        }
    };
?>