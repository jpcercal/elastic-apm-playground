<?php

class Mysql
{
    private $pdo;

    public function __construct()
    {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $dsn = "mysql:host=mysql;dbname=playground;charset=utf8mb4";

        $this->pdo = new PDO($dsn, "root", "root", $options);
    }

    public function dropTable(): void
    {
        $this->pdo->exec(<<<EOT
DROP TABLE IF EXISTS `messages`
EOT
        );
    }

    public function createTable(): void
    {
        $this->pdo->exec(<<<EOT
CREATE TABLE IF NOT EXISTS `messages` (
    `id` INTEGER PRIMARY KEY,
    `message` TEXT
)
EOT
        );
    }

    public function insert(): void
    {
        $id = 1;
        $message = "Message#" . floor(microtime(true));

        $insert = "INSERT INTO `messages` (`id`, `message`) VALUES (:id, :message)";
        $stmt = $this->pdo->prepare($insert);

        // Bind parameters to statement variables
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }

    public function update(): void
    {
        $id = 1;
        $message = "Message#" . rand(100, 999) . '#';

        $insert = "UPDATE `messages` SET `message` = :message WHERE `id` = :id";
        $stmt = $this->pdo->prepare($insert);

        // Bind parameters to statement variables
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
    }

    public function delete(): void
    {
        $id = 1;
        $insert = "DELETE FROM `messages` WHERE `id` = :id";
        $stmt = $this->pdo->prepare($insert);

        // Bind parameters to statement variables
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }

    public function select(): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM `messages`');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
