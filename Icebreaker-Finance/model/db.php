<?php
$iniFilePath = __DIR__ . '/dbconfig.ini';
if (!file_exists($iniFilePath)) {
    die("dbconfig.ini not found at: $iniFilePath");
}

$ini = parse_ini_file($iniFilePath);
if (!$ini) {
    die("Failed to parse dbconfig.ini. Check the file format.");
}

try {
    $db = new PDO(
        "mysql:host={$ini['servername']};port={$ini['port']};dbname={$ini['dbname']}",
        $ini['username'],
        $ini['password']
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    // Debugging
    // echo "Database connection successful.";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>