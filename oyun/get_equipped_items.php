<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $character_id = $_GET['character_id'] ?? null;

    if (!$character_id) {
        echo json_encode([
            'success' => false,
            'message' => 'Karakter ID gerekli'
        ]);
        exit;
    }

    try {
        $stmt = $conn->prepare("
            SELECT 
                ke.*,
                h.esya_adi as helmet_name,
                a.esya_adi as armor_name,
                w.esya_adi as weapon_name,
                b.esya_adi as boots_name
            FROM kusanilan_ekipmanlar ke
            LEFT JOIN esyalar h ON ke.kask_id = h.esya_id
            LEFT JOIN esyalar a ON ke.zirh_id = a.esya_id
            LEFT JOIN esyalar w ON ke.silah_id = w.esya_id
            LEFT JOIN esyalar b ON ke.bot_id = b.esya_id
            WHERE ke.karakter_id = ?
        ");
        
        $stmt->execute([$character_id]);
        $equipment = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($equipment) {
            echo json_encode([
                'success' => true,
                'message' => 'Ekipmanlar başarıyla yüklendi',
                'items' => [
                    'helmet_name' => $equipment['helmet_name'],
                    'armor_name' => $equipment['armor_name'],
                    'weapon_name' => $equipment['weapon_name'],
                    'boots_name' => $equipment['boots_name']
                ]
            ]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'Ekipman bulunamadı',
                'items' => [
                    'helmet_name' => null,
                    'armor_name' => null,
                    'weapon_name' => null,
                    'boots_name' => null
                ]
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