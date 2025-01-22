<?php
require_once 'connect.php';

function getCharacterStats($user_id) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT 
                k.karakter_id,
                k.karakter_adi,
                k.meslek_id,
                m.meslek_adi,
                k.seviye,
                k.deneyim,
                k.dsp,
                k.can,
                k.max_can,
                k.mana,
                k.max_mana,
                k.guc,
                k.dayaniklilik,
                k.ceviklik,
                k.zeka,
                k.para
            FROM karakterler k
            LEFT JOIN meslekler m ON k.meslek_id = m.meslek_id
            WHERE k.kullanici_id = ?
        ");
        
        $stmt->execute([$user_id]);
        $character = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($character) {
            return array(
                "success" => true,
                "data" => $character
            );
        } else {
            return array(
                "success" => false,
                "message" => "Karakter bulunamadı."
            );
        }
    } catch(PDOException $e) {
        return array(
            "success" => false,
            "message" => "Veri çekme hatası: " . $e->getMessage()
        );
    }
}

function updateCharacterStat($karakter_id, $stat_type) {
    global $conn;
    
    $allowed_stats = ['guc', 'dayaniklilik', 'ceviklik', 'zeka'];
    if (!in_array($stat_type, $allowed_stats)) {
        return array(
            "success" => false,
            "message" => "Geçersiz stat türü."
        );
    }
    
    try {
        $conn->beginTransaction();
        
        // DSP kontrolü
        $stmt = $conn->prepare("SELECT dsp FROM karakterler WHERE karakter_id = ?");
        $stmt->execute([$karakter_id]);
        $character = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$character || $character['dsp'] <= 0) {
            $conn->rollBack();
            return array(
                "success" => false,
                "message" => "Dağıtılabilir statü puanı kalmadı."
            );
        }
        
        // Stat güncelleme
        $stmt = $conn->prepare("UPDATE karakterler SET $stat_type = $stat_type + 1, dsp = dsp - 1 WHERE karakter_id = ?");
        $result = $stmt->execute([$karakter_id]);
        
        if ($stmt->rowCount() > 0) {
            $conn->commit();
            return array(
                "success" => true,
                "message" => "Statü başarıyla güncellendi."
            );
        } else {
            $conn->rollBack();
            return array(
                "success" => false,
                "message" => "Güncelleme yapılamadı: Karakter bulunamadı."
            );
        }
        
    } catch(PDOException $e) {
        $conn->rollBack();
        return array(
            "success" => false,
            "message" => "Güncelleme hatası: " . $e->getMessage()
        );
    }
}

// Ana endpoint mantığı
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? '';
    
    if (empty($user_id)) {
        echo json_encode(array("success" => false, "message" => "Kullanıcı ID gereklidir."));
        exit;
    }
    
    $result = getCharacterStats($user_id);
    echo json_encode($result);
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $karakter_id = $data['karakter_id'] ?? '';
    $stat_type = $data['stat_type'] ?? '';
    
    if (empty($karakter_id) || empty($stat_type)) {
        echo json_encode(array("success" => false, "message" => "Gerekli bilgiler eksik."));
        exit;
    }
    
    $result = updateCharacterStat($karakter_id, $stat_type);
    echo json_encode($result);
}
?>