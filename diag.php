<?php
chdir(__DIR__);
require __DIR__ . '/vendor/autoload.php';
define('ENVIRONMENT', 'testing');
require __DIR__ . '/vendor/codeigniter4/framework/system/Test/bootstrap.php';

$config = new \Config\Database();
echo "defaultGroup: " . $config->defaultGroup . "\n";
$tests = $config->tests;
echo "tests DBPrefix: " . ($tests['DBPrefix'] ?? '(none)') . "\n";
echo "tests database: " . ($tests['database'] ?? '(none)') . "\n";
echo "tests DBDriver: " . ($tests['DBDriver'] ?? '(none)') . "\n";

$conn = \Config\Database::connect('tests');
$conn->initialize();
echo "Connection DBPrefix: " . $conn->DBPrefix . "\n";
echo "Connection DBDriver: " . $conn->DBDriver . "\n";

$tables = $conn->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name")->getResultArray();
foreach ($tables as $t) {
    echo "table: " . $t['name'] . "\n";
}
