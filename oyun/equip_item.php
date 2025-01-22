<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!isset($data['karakter_id']) || !isset($data['esya_id']) || !isset($data['esya_turu'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Gerekli alanlar eksik'
        ]);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Önce karakterin mevcut durumunu al
        $stmt = $conn->prepare("SELECT can, max_can, mana, max_mana FROM karakterler WHERE karakter_id = ?");
        $stmt->execute([$data['karakter_id']]);
        $karakter = $stmt->fetch(PDO::FETCH_ASSOC);

        // Yeni ekipmanın özelliklerini al
        $stmt = $conn->prepare("
            SELECT can_bonusu, mana_bonusu, guc_bonusu, 
                   ceviklik_bonusu, dayaniklilik_bonusu, zeka_bonusu 
            FROM esyalar 
            WHERE esya_id = ?");
        $stmt->execute([$data['esya_id']]);
        $yeni_esya = $stmt->fetch(PDO::FETCH_ASSOC);

        // Mevcut kuşanılan eşyayı kontrol et
        $stmt = $conn->prepare("SELECT * FROM kusanilan_ekipmanlar WHERE karakter_id = ?");
        $stmt->execute([$data['karakter_id']]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        $column = strtolower($data['esya_turu']) . '_id';
        $mevcut_esya_id = null;

        // Yeni can ve mana değerlerini hesapla
        $yeni_max_can = $karakter['max_can'];
        $yeni_max_mana = $karakter['max_mana'];
        $yeni_can = $karakter['can'];
        $yeni_mana = $karakter['mana'];

        if ($existing) {
            $mevcut_esya_id = $existing[$column];

            if ($mevcut_esya_id) {
                // Mevcut eşyanın özelliklerini al
                $stmt = $conn->prepare("
                    SELECT can_bonusu, mana_bonusu, guc_bonusu, 
                           ceviklik_bonusu, dayaniklilik_bonusu, zeka_bonusu 
                    FROM esyalar 
                    WHERE esya_id = ?");
                $stmt->execute([$mevcut_esya_id]);
                $eski_esya = $stmt->fetch(PDO::FETCH_ASSOC);

                // Eski eşyanın özelliklerini çıkar
                $yeni_max_can -= $eski_esya['can_bonusu'];
                $yeni_max_mana -= $eski_esya['mana_bonusu'];
                
                // Mevcut can ve mana değerlerini oransal olarak ayarla
                if ($karakter['max_can'] > 0) {
                    $can_oran = $karakter['can'] / $karakter['max_can'];
                    $yeni_can = round($yeni_max_can * $can_oran);
                }
                if ($karakter['max_mana'] > 0) {
                    $mana_oran = $karakter['mana'] / $karakter['max_mana'];
                    $yeni_mana = round($yeni_max_mana * $mana_oran);
                }

                // Mevcut eşyayı envantere geri koy
                $stmt = $conn->prepare("
                    UPDATE envanter 
                    SET esya_id = ? 
                    WHERE karakter_id = ? AND esya_id IS NULL 
                    LIMIT 1");
                $stmt->execute([$mevcut_esya_id, $data['karakter_id']]);
            }
        }

        // Yeni eşyanın özelliklerini ekle
        $yeni_max_can += $yeni_esya['can_bonusu'];
        $yeni_max_mana += $yeni_esya['mana_bonusu'];

        // Can ve mana değerlerini oransal olarak artır
        if ($karakter['max_can'] > 0) {
            $can_oran = $karakter['can'] / $karakter['max_can'];
            $yeni_can = round($yeni_max_can * $can_oran);
        }
        if ($karakter['max_mana'] > 0) {
            $mana_oran = $karakter['mana'] / $karakter['max_mana'];
            $yeni_mana = round($yeni_max_mana * $mana_oran);
        }

        // Yeni eşyayı envanterden kaldır
        $stmt = $conn->prepare("DELETE FROM envanter WHERE karakter_id = ? AND esya_id = ? LIMIT 1");
        $stmt->execute([$data['karakter_id'], $data['esya_id']]);

        // Karakter özelliklerini güncelle
        $stmt = $conn->prepare("
            UPDATE karakterler 
            SET can = ?,
                max_can = ?,
                mana = ?,
                max_mana = ?,
                guc = guc - COALESCE((SELECT guc_bonusu FROM esyalar WHERE esya_id = ?), 0) + ?,
                ceviklik = ceviklik - COALESCE((SELECT ceviklik_bonusu FROM esyalar WHERE esya_id = ?), 0) + ?,
                dayaniklilik = dayaniklilik - COALESCE((SELECT dayaniklilik_bonusu FROM esyalar WHERE esya_id = ?), 0) + ?,
                zeka = zeka - COALESCE((SELECT zeka_bonusu FROM esyalar WHERE esya_id = ?), 0) + ?
            WHERE karakter_id = ?");
        
        $stmt->execute([
            $yeni_can,
            $yeni_max_can,
            $yeni_mana,
            $yeni_max_mana,
            $mevcut_esya_id,
            $yeni_esya['guc_bonusu'],
            $mevcut_esya_id,
            $yeni_esya['ceviklik_bonusu'],
            $mevcut_esya_id,
            $yeni_esya['dayaniklilik_bonusu'],
            $mevcut_esya_id,
            $yeni_esya['zeka_bonusu'],
            $data['karakter_id']
        ]);

        // Kuşanılan ekipmanı güncelle
        if ($existing) {
            $stmt = $conn->prepare("UPDATE kusanilan_ekipmanlar SET $column = ? WHERE karakter_id = ?");
            $stmt->execute([$data['esya_id'], $data['karakter_id']]);
        } else {
            $stmt = $conn->prepare("INSERT INTO kusanilan_ekipmanlar (karakter_id, $column) VALUES (?, ?)");
            $stmt->execute([$data['karakter_id'], $data['esya_id']]);
        }

        $conn->commit();

        // Güncellenmiş karakter özelliklerini al
        $stmt = $conn->prepare("
            SELECT karakter_id, karakter_adi, meslek_id, seviye, deneyim, dsp,
                   can, max_can, mana, max_mana, guc, dayaniklilik, ceviklik, zeka
            FROM karakterler 
            WHERE karakter_id = ?");
        $stmt->execute([$data['karakter_id']]);
        $updated_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'message' => 'Eşya başarıyla kuşanıldı',
            'updated_stats' => $updated_stats
        ]);
        
    } catch(PDOException $e) {
        $conn->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Veritabanı hatası: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek metodu'
    ]);
}
?>