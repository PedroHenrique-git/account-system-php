<?php
    class Account {
        public $id;
        public $owner;
        public $document;
        public $password;
        public $amount;

        public function __construct($id, $owner, $document, $password, $amount) {
            $this->id = $id;
            $this->owner = $owner;
            $this->document = $document;
            $this->password = $password;
            $this->amount = $amount;
        }
    }