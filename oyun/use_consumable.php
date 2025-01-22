<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['karakter_id']) || !isset($data['esya_id'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Gerekli alanlar eksik'
        ]);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Önce karakter bilgilerini al
        $stmt = $conn->prepare("SELECT can, max_can, mana, max_mana FROM karakterler WHERE karakter_id = ?");
        $stmt->execute([$data['karakter_id']]);
        $character = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$character) {
            throw new Exception("Karakter bulunamadı!");
        }

        // Eşya bilgilerini al
        $stmt = $conn->prepare("SELECT * FROM esyalar WHERE esya_id = ? AND esya_turu = 'TUKETILEBILIR'");
        $stmt->execute([$data['esya_id']]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            throw new Exception("Geçersiz eşya veya tüketilebilir değil!");
        }

        // Can ve mana kontrolü
        if ($item['can_bonusu'] > 0 && $character['can'] >= $character['max_can']) {
            echo json_encode([
                'success' => false,
                'message' => 'Can barınız zaten dolu!'
            ]);
            exit;
        }

        if ($item['mana_bonusu'] > 0 && $character['mana'] >= $character['max_mana']) {
            echo json_encode([
                'success' => false,
                'message' => 'Mana barınız zaten dolu!'
            ]);
            exit;
        }

        // Eşyanın etkilerini uygula
        $newHealth = min($character['can'] + $item['can_bonusu'], $character['max_can']);
        $newMana = min($character['mana'] + $item['mana_bonusu'], $character['max_mana']);

        // Karakter değerlerini güncelle
        $stmt = $conn->prepare("UPDATE karakterler SET can = ?, mana = ? WHERE karakter_id = ?");
        $stmt->execute([$newHealth, $newMana, $data['karakter_id']]);

        // Eşyayı envanterden kaldır
        $stmt = $conn->prepare("DELETE FROM envanter WHERE karakter_id = ? AND esya_id = ? LIMIT 1");
        $stmt->execute([$data['karakter_id'], $data['esya_id']]);

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Eşya başarıyla kullanıldı',
            'healAmount' => $newHealth - $character['can'],
            'manaAmount' => $newMana - $character['mana']
        ]);
    } catch(Exception $e) {
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek metodu'
    ]);
}
?>