<?php
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(array(
            "success" => false, 
            "message" => "Kullanıcı adı ve şifre gereklidir.",
            "userId" => -1,
            "characterId" => -1
        ));
        exit;
    }
    
    try {
        // Kullanıcı ve karakter bilgilerini birlikte al
        $stmt = $conn->prepare("
            SELECT k.kullanici_id, k.sifre_hash, c.karakter_id 
            FROM kullanicilar k 
            LEFT JOIN karakterler c ON k.kullanici_id = c.kullanici_id 
            WHERE k.kullanici_adi = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['sifre_hash'])) {
            echo json_encode(array(
                "success" => true,
                "message" => "Giriş başarılı.",
                "userId" => $user['kullanici_id'],
                "characterId" => $user['karakter_id']
            ));
        } else {
            echo json_encode(array(
                "success" => false, 
                "message" => "Geçersiz kullanıcı adı veya şifre.",
                "userId" => -1,
                "characterId" => -1
            ));
        }
    } catch(PDOException $e) {
        echo json_encode(array(
            "success" => false, 
            "message" => "Giriş hatası: " . $e->getMessage(),
            "userId" => -1,
            "characterId" => -1
        ));
    }
}
?>