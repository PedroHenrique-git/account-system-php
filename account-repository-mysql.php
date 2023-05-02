<?php
    require_once 'account-repository.php';
    require_once 'account.php';
    require_once 'account-repository-exception.php';

    class AccountRepositoryMysql implements AccountRepository {
        private PDO | NULL $pdo = null; 

        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        public function withdraw($document, $password, $amount) {
            try {
                $sql = 'UPDATE account SET amount = amount - :valor where document = :document and password = :password and amount >= :amount';

                $ps =$this->pdo->prepare($sql);

                $ps->execute([
                    'valor' => $amount,
                    'document' => $document,
                    'password' => $password,
                    'amount' => $amount
                ]);

                if($ps->rowCount() < 1) {
                    throw new AccountRepositoryException("Account not found or Insufficient amount", 404);
                }
            } catch(PDOException $err) {
                throw new AccountRepositoryException("Error when trying to withdraw", 500, $err);
            }    
        }

        public function deposit($document, $password, $amount) {
            try {
                $sql = 'UPDATE account SET amount = amount + :valor where document = :document and password = :password';

                $ps =$this->pdo->prepare($sql);

                $ps->execute([
                    'valor' => $amount,
                    'document' => $document,
                    'password' => $password
                ]);

                if($ps->rowCount() < 1) {
                    throw new AccountRepositoryException("Account not found", 404);
                }
            } catch(PDOException $err) {
                throw new AccountRepositoryException("Error when trying to deposit", 500, $err);
            }    
        }

        public function transference(Account $sender, Account $receiver) {
            try {
                $this->pdo->beginTransaction();

                $this->withdraw(
                    $sender->document,
                    $sender->password,
                    $sender->amount
                );

                $this->deposit(
                    $receiver->document,
                    $receiver->password,
                    $sender->amount
                );

                $this->pdo->commit();
            } catch(PDOException $err) {
                $this->pdo->rollBack();

                throw new AccountRepositoryException("Error when trying to transfer", 500, $err);
            }
        }

        public function createAccount(Account $account) {
            try {
                $sql = 'INSERT INTO account(owner, document, password) values(?, ?, ?)';

                $ps =$this->pdo->prepare($sql);

                $ps->execute([
                    $account->owner,
                    $account->document,
                    $account->password
                ]);
            } catch(PDOException $err) {
                throw new AccountRepositoryException("Error when trying to create a new account", 500, $err);
            }
        }

        public function listAccounts() {
            try {
                $sql = 'SELECT id, owner, document, amount from account';
                $accounts = [];

                $ps =$this->pdo->prepare($sql);
                $ps->setFetchMode(PDO::FETCH_ASSOC);
                $ps->execute();
                
                foreach ($ps as $reg) {
                    array_push(
                        $accounts, 
                        new Account(
                            $reg['id'],
                            $reg['owner'],
                            $reg['document'],
                            '',
                            $reg['amount'],
                        )
                    );
                }

                return $accounts;
            } catch(PDOException $err) {
                throw new AccountRepositoryException("Error when trying to list accounts", 500, $err);
            }
        }
    }