<?php
    require_once "account.php";

    interface AccountRepository {
        public function createAccount(Account $account);
        public function listAccounts();
        public function deposit($document, $password, $amount);
        public function withdraw($document, $password, $amount);
        public function transference(Account $sender, Account $receiver);
    }