<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['user_id'] ?? '';

    if (empty($userId)) {
        echo json_encode(array(
            "success" => false,
            "message" => "Kullanıcı ID gerekli.",
            "items" => []
        ));
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT e.esya_id, e.esya_adi
            FROM envanter env
            JOIN esyalar e ON env.esya_id = e.esya_id
            JOIN karakterler k ON env.karakter_id = k.karakter_id
            WHERE k.kullanici_id = ?
            LIMIT 9
        ");
        
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array(
            "success" => true,
            "message" => "",
            "items" => $items
        ));
    } catch(PDOException $e) {
        echo json_encode(array(
            "success" => false,
            "message" => "Envanter yükleme hatası: " . $e->getMessage(),
            "items" => []
        ));
    }
}
?>