<?php
    require_once 'pdo-singleton.php';
    require_once 'account-repository-mysql.php';

    $pdo = PdoSingleton::getInstance();
    $repository = new AccountRepositoryMysql($pdo);
    $option = '0';

    const CREATE_ACCOUNT = '1';
    const LIST_ACCOUNTS = '2';
    const DEPOSIT = '3';
    const TRANSFERENCE = '4';
    const EXIT_PROGRAM = '0';

    function menu() {
        echo PHP_EOL;
        echo "1 - create account" . PHP_EOL;
        echo "2 - list accounts" . PHP_EOL;
        echo "3 - deposit" . PHP_EOL;
        echo "4 - transference" . PHP_EOL;
        echo "0 - exit" . PHP_EOL;
        echo PHP_EOL;
    }

    function customHash($content) {
        return hash('sha1', 'k8Qv@YKp4Aj5' . $content . '95P*u0GMlaw8');
    }

    function transference($repository) {
        echo PHP_EOL . 'Sender' . PHP_EOL;

        $senderDocument = readline("Enter the document: ");
        $senderPassword = readline("Enter the password: ");
        $senderAmount = (float) readline("Enter the amount: ");

        $sender = new Account(0, '', $senderDocument, customHash($senderPassword), $senderAmount);

        echo PHP_EOL . 'Receiver' . PHP_EOL;

        $receiverDocument = readline("Enter the document: ");
        $receiverPassword = readline("Enter the password: ");

        $receiver = new Account(0, '', $receiverDocument, customHash($receiverPassword), 0);

        $repository->transference($sender, $receiver);
    }

    function createAccount($repository) {
        $owner = readline("Enter your owner: ");
        $document = readline("Enter your document: ");
        $password = readline("Enter your password: ");
    
        $account = new Account(0, $owner, $document, customHash($password), 0);

        $repository->createAccount($account);
    }

    function deposit($repository) {
        $document = readline("Enter your document: ");
        $password = readline("Enter your password: ");
        $amount = (float) readline("Enter the amount: ");

        $repository->deposit($document, customHash($password), $amount);
    }

    function listAccounts($repository) {
        echo PHP_EOL . 'ACCOUNTS' . PHP_EOL;

        foreach ($repository->listAccounts() as $account) {
            $owner = $account->owner;
            $document = $account->document;
            $amount = $account->amount;

            echo "owner: $owner, document: $document, amount: $amount" . PHP_EOL;
        }
    }

    function init(&$option, $repository) {
        do {
            menu();

            $option = readline("Choose an option: ");

            try {
                switch ($option) {
                    case CREATE_ACCOUNT:
                        createAccount($repository);
                        break;
                    case LIST_ACCOUNTS:
                        listAccounts($repository);
                        break;
                    case DEPOSIT:
                        deposit($repository);
                        break;
                    case TRANSFERENCE:
                        transference($repository);
                        break;
                    case EXIT_PROGRAM:
                        exit();
                        break;
                    default:
                        echo PHP_EOL . "Option not found" . PHP_EOL;
                        break;
                }
            } catch(AccountRepositoryException $err) {
                echo PHP_EOL . $err->getMessage() . PHP_EOL;
            }
        } while ($option !== EXIT_PROGRAM);
    }
    
    function createTable($pdo) {
        $sql = <<<CREATE_TABLE
            CREATE TABLE IF NOT EXISTS account(
                id int primary key auto_increment,
                owner varchar(100) not null, 
                document varchar(11) not null, 
                password varchar(255) not null, 
                amount double(10, 2) default 0
            );
        CREATE_TABLE;
    
        $ps = $pdo->prepare($sql);
        $ps->execute();
    }

    createTable($pdo);
    init($option, $repository);