<?php
declare(strict_types=1);

function logActivity(int $userId, string $action): void {
    $ip = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
        ? $_SERVER['REMOTE_ADDR']
        : 'unknown';
    $agent = isset($_SERVER['HTTP_USER_AGENT']) && is_string($_SERVER['HTTP_USER_AGENT'])
        ? $_SERVER['HTTP_USER_AGENT']
        : 'unknown';

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=myapp;charset=utf8", "username", "password");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("
            INSERT INTO user_activity (user_id, action, ip_address, user_agent)
            VALUES (:user_id, :action, :ip, :agent)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':action'  => $action,
            ':ip'      => $ip,
            ':agent'   => $agent
        ]);
    } catch (PDOException $e) {
        error_log("Database error in logActivity: " . $e->getMessage());
    }
}

$pdo = new PDO("mysql:host=localhost;dbname=myapp;charset=utf8", "username", "password");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userId = 123;

$lastIpStmt = $pdo->prepare("
    SELECT ip_address 
    FROM user_activity 
    WHERE user_id = :user_id 
    ORDER BY created_at DESC 
    LIMIT 1
");
$lastIpStmt->execute([':user_id' => $userId]);
$lastIp = $lastIpStmt->fetchColumn();

$currentIp = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
    ? $_SERVER['REMOTE_ADDR']
    : 'unknown';

if (is_string($lastIp) && $lastIp !== $currentIp) {
    logActivity($userId, 'Login from new IP detected: ' . $currentIp);
}

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM user_activity 
    WHERE user_id = :user_id 
      AND action = 'Login failed' 
      AND created_at > NOW() - INTERVAL 15 MINUTE
");
$stmt->execute([':user_id' => $userId]);
$failedCountRaw = $stmt->fetchColumn();
$failedCount = is_numeric($failedCountRaw) ? (int)$failedCountRaw : 0;

if ($failedCount > 5) {
    logActivity($userId, 'Too many failed login attempts');
}
?>
