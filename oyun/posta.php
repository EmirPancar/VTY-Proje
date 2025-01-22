<?php
require_once 'connect.php';

// Debug log fonksiyonu
function debug_log($message) {
    error_log(print_r($message, true));
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $userId = $_GET['user_id'] ?? '';

    if (empty($userId)) {
        echo json_encode(array(
            "success" => false, 
            "message" => "Kullanıcı ID gerekli.", 
            "postalar" => []
        ));
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT 
                p.posta_id,
                p.gonderen_karakter_id,
                p.alici_karakter_id,
                p.esya_id,
                p.mesaj,
                p.gonderilme_tarihi,
                p.okundu,
                e.esya_adi
            FROM postalar p 
            LEFT JOIN esyalar e ON p.esya_id = e.esya_id 
            JOIN karakterler k ON p.alici_karakter_id = k.karakter_id 
            WHERE k.kullanici_id = ?
            ORDER BY p.gonderilme_tarihi DESC
        ");
        
        $stmt->execute([$userId]);
        $postalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(array(
            "success" => true,
            "message" => "",
            "postalar" => array_map(function($posta) {
                return array(
                    "posta_id" => (int)$posta['posta_id'],
                    "gonderen_karakter_id" => (int)$posta['gonderen_karakter_id'],
                    "alici_karakter_id" => (int)$posta['alici_karakter_id'],
                    "esya_id" => $posta['esya_id'] ? (int)$posta['esya_id'] : null,
                    "mesaj" => $posta['mesaj'],
                    "gonderilme_tarihi" => $posta['gonderilme_tarihi'],
                    "okundu" => (int)$posta['okundu'],
                    "esya_adi" => $posta['esya_adi'] ?? ""
                );
            }, $postalar)
        ));
    } catch(PDOException $e) {
        debug_log("GET PDO Hatası: " . $e->getMessage());
        echo json_encode(array(
            "success" => false, 
            "message" => "Posta çekme hatası: " . $e->getMessage(),
            "postalar" => []
        ));
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_log("POST isteği alındı");
    
    $raw_data = file_get_contents('php://input');
    debug_log("Alınan raw data: " . $raw_data);
    
    $data = json_decode($raw_data, true);
    debug_log("Decode edilen data: " . print_r($data, true));
    
    $postaId = $data['posta_id'] ?? '';
    $esyaId = $data['esya_id'] ?? null;  // 0 yerine null kullanıyoruz

    debug_log("PostaId: " . $postaId . ", EsyaId: " . $esyaId);

    if (empty($postaId)) {
        debug_log("Posta ID boş!");
        echo json_encode(array("success" => false, "message" => "Posta ID gerekli."));
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1. Önce postayı getir ve kontrol et
        $stmt = $conn->prepare("SELECT esya_id, alici_karakter_id FROM postalar WHERE posta_id = ?");
        $stmt->execute([$postaId]);
        $posta = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$posta) {
            throw new Exception("Posta bulunamadı.");
        }

        // Eğer postada eşya varsa
        if ($posta['esya_id'] !== null) {
            // 2. Kullanıcının envanterinde boş slot bul
            $stmt = $conn->prepare("
                SELECT MIN(slot_numarasi) as bos_slot 
                FROM envanter 
                WHERE karakter_id = ? 
                AND esya_id IS NULL
            ");
            $stmt->execute([$posta['alici_karakter_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['bos_slot'] !== null) {
                // 3. Boş slota eşyayı yerleştir
                $stmt = $conn->prepare("
                    UPDATE envanter 
                    SET esya_id = ? 
                    WHERE karakter_id = ? 
                    AND slot_numarasi = ?
                ");
                $stmt->execute([
                    $posta['esya_id'],
                    $posta['alici_karakter_id'],
                    $result['bos_slot']
                ]);

                // 4. Postadaki eşyayı NULL yap
                $stmt = $conn->prepare("
                    UPDATE postalar 
                    SET esya_id = NULL 
                    WHERE posta_id = ?
                ");
                $stmt->execute([$postaId]);
            } else {
                throw new Exception("Envanterde boş yer bulunamadı!");
            }
        }

        // 5. Postayı okundu olarak işaretle
        $stmt = $conn->prepare("
            UPDATE postalar 
            SET okundu = 1 
            WHERE posta_id = ?
        ");
        $stmt->execute([$postaId]);

        $conn->commit();
        echo json_encode(array("success" => true, "message" => "Posta başarıyla işaretlendi."));

    } catch (Exception $e) {
        $conn->rollBack();
        debug_log("Hata: " . $e->getMessage());
        echo json_encode(array("success" => false, "message" => $e->getMessage()));
    }
}
?>