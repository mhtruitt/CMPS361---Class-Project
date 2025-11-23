<?php
declare(strict_types=1);

 * @param int $userId  The ID of the user performing the action.
 * @param string $action  A description of the action being performed.
 * @return void
 */
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

        $logFile = __DIR__ . '/activity.log';
        $logEntry = sprintf(
            "%s | User: %d | Action: %s | IP: %s | Agent: %s\n",
            date('Y-m-d H:i:s'),
            $userId,
            $action,
            $ip,
            $agent
        );
        file_put_contents($logFile, $logEntry, FILE_APPEND);

    } catch (PDOException $e) {
        error_log("Database error in logActivity: " . $e->getMessage());
    }
}
?>
