<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $conn->prepare("SELECT meslek_id, meslek_adi FROM meslekler ORDER BY meslek_adi");
        $stmt->execute();
        $professions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(array(
            "success" => true,
            "data" => $professions
        ));
    } catch(PDOException $e) {
        echo json_encode(array(
            "success" => false,
            "message" => "Meslek listesi alınamadı: " . $e->getMessage()
        ));
    }
}
?>