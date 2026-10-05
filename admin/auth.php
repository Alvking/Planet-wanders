<?php
// admin/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db/db.php';

// Universal DB handle supporting PDO or MySQLi
$db = $conn ?? $pdo ?? null;
if (!$db) {
    die("Database connection failed. Ensure db/db.php defines \$conn or \$pdo.");
}

function db_query($db, $sql, $params = []) {
    if ($db instanceof mysqli) {
        if (empty($params)) {
            $res = $db->query($sql);
            return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        }
        $stmt = $db->prepare($sql);
        $types = '';
        foreach ($params as $p) {
            $types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
        }
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    } elseif ($db instanceof PDO) {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    return [];
}

function db_execute($db, $sql, $params = []) {
    if ($db instanceof mysqli) {
        if (empty($params)) {
            return $db->query($sql);
        }
        $stmt = $db->prepare($sql);
        $types = '';
        foreach ($params as $p) {
            $types .= is_int($p) ? 'i' : (is_float($p) ? 'd' : 's');
        }
        $stmt->bind_param($types, ...$params);
        return $stmt->execute();
    } elseif ($db instanceof PDO) {
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    }
    return false;
}

function check_admin_auth() {
    if (!isset($_SESSION['pw_admin_id'])) {
        header("Location: login.php");
        exit;
    }
}

function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}