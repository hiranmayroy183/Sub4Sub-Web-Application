<?php
// db.php
$host = 'localhost';
$db   = 'sub4sub';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Create users table if it doesn't exist
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        location_address VARCHAR(255) NOT NULL,
        youtube_channel VARCHAR(255) NOT NULL,
        youtube_channel_changed BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createTableSQL);

    // Add youtube_channel_changed column if it does not exist
    $addColumnSQL = "ALTER TABLE users ADD COLUMN IF NOT EXISTS youtube_channel_changed BOOLEAN DEFAULT FALSE";
    $pdo->exec($addColumnSQL);

} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
