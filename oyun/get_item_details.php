<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $item_id = $_GET['item_id'] ?? null;

    if (!$item_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Eşya ID gerekli'
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT 
                esya_id,
                esya_adi,
                esya_turu,
                nadirlik,
                seviye_gereksinimi,
                aciklama,
                can_bonusu,
                mana_bonusu,
                guc_bonusu,
                ceviklik_bonusu,
                dayaniklilik_bonusu,
                zeka_bonusu
            FROM esyalar 
            WHERE esya_id = ?
        ");
        
        $stmt->execute([$item_id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // esya_turu değerini kontrol et ve düzenle
            if ($item['esya_turu'] === 'İksir' || $item['esya_turu'] === 'iksir') {
                $item['esya_turu'] = 'Tüketilebilir';
            }

            echo json_encode([
                'success' => true,
                'message' => 'Eşya detayları başarıyla alındı',
                'item' => $item
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Eşya bulunamadı'
            ]);
        }
    } catch(PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Veritabanı hatası: ' . $e->getMessage()
        ]);
    }
}
?>