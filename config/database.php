<?php
/**
 * Fpesa Loan Platform - Database Configuration
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'vxjtgclw_loans');
define('DB_USER', 'vxjtgclw_loans');
define('DB_PASS', '?zzbH8geE5$F{(gL');
define('DB_CHARSET', 'utf8mb4');

// Base URL - change this for your domain
define('BASE_URL', '');

class Database {
    private static ?PDO $instance = null;
    public static function connect(): PDO {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $opts = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opts);
            } catch (PDOException $e) {
                error_log("DB Error: " . $e->getMessage());
                die('<div style="font-family:sans-serif;padding:40px;text-align:center"><h2>Database Error</h2><p>Could not connect to database. Please check config/database.php</p></div>');
            }
        }
        return self::$instance;
    }
}
