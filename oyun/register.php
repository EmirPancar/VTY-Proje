<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = $data['username'] ?? '';
    $chrname = $data['chrname'] ?? '';
    $password = $data['password'] ?? '';
    $meslek = $data['meslek'] ?? '';
    
    if (empty($username) || empty($chrname) || empty($password) || empty($meslek)) {
        echo json_encode(array("success" => false, "message" => "Tüm alanların doldurulması gereklidir."));
        exit;
    }
    
    // Meslek ID'sini belirle
    $meslek_id = ($meslek === 'Savaşçı') ? 1 : 2;
    $baslangic_seviye = 1;
    
    try {
        // Transaction başlat
        $conn->beginTransaction();

        // Mesleğe göre başlangıç değerlerini belirle
        if ($meslek_id == 1) { // Savaşçı
            $guc = 5;
            $dayaniklilik = 5;
            $ceviklik = 3;
            $zeka = 2;
            $max_can = 120;
            $max_mana = 30;
        } else { // Büyücü
            $guc = 2;
            $dayaniklilik = 3;
            $ceviklik = 3;
            $zeka = 7;
            $max_can = 80;
            $max_mana = 100;
        }

        // Kullanıcı kaydı
        $stmt = $conn->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre_hash) VALUES (?, ?)");
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);
        $user_id = $conn->lastInsertId();

        // Karakter kaydı
        $stmt = $conn->prepare("
            INSERT INTO karakterler 
            (kullanici_id, karakter_adi, meslek_id, seviye, guc, dayaniklilik, ceviklik, zeka, can, max_can, mana, max_mana) 
            VALUES 
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id, 
            $chrname, 
            $meslek_id,
            $baslangic_seviye,
            $guc,
            $dayaniklilik,
            $ceviklik,
            $zeka,
            $max_can-20,
            $max_can,
            $max_mana,
            $max_mana-20
        ]);
        $character_id = $conn->lastInsertId();


        // Envanter slotlarını oluştur (çanta tablosu artık kullanılmıyor)
        $stmt = $conn->prepare("INSERT INTO envanter (karakter_id, slot_numarasi, esya_id) VALUES (?, ?, NULL)");
        for ($slot = 1; $slot <= 9; $slot++) {
            $stmt->execute([$character_id, $slot]);
        }

        // Başlangıç eşyalarını postalar tablosuna ekle
        $stmt = $conn->prepare("
        SELECT e.esya_id, e.esya_adi
        FROM esyalar e
        WHERE (e.meslek_id = ? OR e.meslek_id = 0)
        AND e.seviye_gereksinimi <= ?
        ");
        $stmt->execute([$meslek_id, $baslangic_seviye]);
        $baslangic_esyalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Her başlangıç eşyası için posta gönder
        foreach ($baslangic_esyalar as $esya) {
        $stmt = $conn->prepare("
            INSERT INTO postalar (gonderen_karakter_id, alici_karakter_id, mesaj, esya_id, okundu)
            VALUES (999, ?, ?, ?, 0)
        ");

        $mesaj = "Hoş geldin şampiyon! Bu {$esya['esya_adi']} senin başlangıç eşyan olarak verildi. İyi eğlenceler!";

        $stmt->execute([
            $character_id,      // alici_karakter_id
            $mesaj,            // mesaj
            $esya['esya_id']   // esya_id
        ]);
        }

        // Transaction'ı tamamla
        $conn->commit();
        
        echo json_encode(array(
            "success" => true, 
            "message" => "Kayıt başarıyla tamamlandı, başlangıç eşyaları postalarınıza gönderildi."
        ));
        
    } catch (PDOException $e) {
        // Hata durumunda rollback yap
        $conn->rollBack();
        error_log("PDO Hatası: " . $e->getMessage());
        echo json_encode(array(
            "success" => false, 
            "message" => "Kayıt işlemi sırasında bir hata oluştu: " . $e->getMessage()
        ));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Geçersiz istek metodu."));
}
?>