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
    $createUsersTableSQL = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        location_address VARCHAR(255) NOT NULL,
        youtube_channel VARCHAR(255) NOT NULL,
        youtube_channel_name VARCHAR(255) NOT NULL,
        youtube_channel_changed BOOLEAN DEFAULT FALSE,
        subscription_urls TEXT DEFAULT NULL,
        profile_picture VARCHAR(255) DEFAULT NULL,
        banned BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createUsersTableSQL);

    // Add missing columns if they do not exist
    $addColumnSQLs = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS youtube_channel_name VARCHAR(255) NOT NULL",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS youtube_channel_changed BOOLEAN DEFAULT FALSE",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS subscription_urls TEXT DEFAULT NULL",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_picture VARCHAR(255) DEFAULT NULL",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS banned BOOLEAN DEFAULT FALSE"
    ];

    foreach ($addColumnSQLs as $sql) {
        $pdo->exec($sql);
    }

    // Create user_uploads table if it doesn't exist
    $createUserUploadsTableSQL = "
    CREATE TABLE IF NOT EXISTS user_uploads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        youtube_channel_name VARCHAR(255) NOT NULL,
        youtube_channel_link VARCHAR(255) NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        validated BOOLEAN DEFAULT FALSE,
        reverify BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createUserUploadsTableSQL);

    // Create notifications table if it doesn't exist
    $createNotificationsTableSQL = "
    CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createNotificationsTableSQL);

    // Create about page content table
    $createAboutTableSQL = "
    CREATE TABLE IF NOT EXISTS about_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createAboutTableSQL);

    // Insert initial content if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM about_content");
    if ($stmt->fetchColumn() == 0) {
        $initialContent = "<h1>About Us</h1><p>Welcome to our website. Here is some information about us.</p>";
        $pdo->prepare("INSERT INTO about_content (content) VALUES (?)")->execute([$initialContent]);
    }

    // Create contact page content table
    $createContactTableSQL = "
    CREATE TABLE IF NOT EXISTS contact_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createContactTableSQL);

    // Insert initial content if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_content");
    if ($stmt->fetchColumn() == 0) {
        $initialContent = "<h1>Contact Us</h1><p>Here is how you can contact us.</p>";
        $pdo->prepare("INSERT INTO contact_content (content) VALUES (?)")->execute([$initialContent]);
    }

    // Create terms of service content table
    $createTOSTableSQL = "
    CREATE TABLE IF NOT EXISTS tos_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createTOSTableSQL);

    // Insert initial content if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM tos_content");
    if ($stmt->fetchColumn() == 0) {
        $initialContent = "<h1>Terms of Service</h1><p>Here are our terms of service.</p>";
        $pdo->prepare("INSERT INTO tos_content (content) VALUES (?)")->execute([$initialContent]);
    }

    // Create privacy policy content table
    $createPrivacyTableSQL = "
    CREATE TABLE IF NOT EXISTS privacy_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createPrivacyTableSQL);

    // Insert initial content if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM privacy_content");
    if ($stmt->fetchColumn() == 0) {
        $initialContent = "<h1>Privacy Policy</h1><p>Here is our privacy policy.</p>";
        $pdo->prepare("INSERT INTO privacy_content (content) VALUES (?)")->execute([$initialContent]);
    }

    // Create faqs content table
    $createFAQTableSQL = "
    CREATE TABLE IF NOT EXISTS faq_content (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createFAQTableSQL);

    // Insert initial content if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM faq_content");
    if ($stmt->fetchColumn() == 0) {
        $initialContent = "<h1>FAQ</h1><p>Here are some frequently asked questions.</p>";
        $pdo->prepare("INSERT INTO faq_content (content) VALUES (?)")->execute([$initialContent]);
    }

    // Create admin table
    $createAdminTableSQL = "
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createAdminTableSQL);

    // Create purchase_requests table if it doesn't exist
    $createPurchaseRequestsTableSQL = "
    CREATE TABLE IF NOT EXISTS purchase_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        package VARCHAR(50) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        replied BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    ) ENGINE=INNODB;
    ";

    $pdo->exec($createPurchaseRequestsTableSQL);

    $addRepliedColumnSQL = "
ALTER TABLE purchase_requests 
ADD COLUMN IF NOT EXISTS replied BOOLEAN DEFAULT FALSE;
";
$pdo->exec($addRepliedColumnSQL);


    // Insert initial admin user if not exists
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin");
    if ($stmt->fetchColumn() == 0) {
        $initialAdminUsername = 'admin';
        $initialAdminPassword = password_hash('password', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)")->execute([$initialAdminUsername, $initialAdminPassword]);
    }

} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>
