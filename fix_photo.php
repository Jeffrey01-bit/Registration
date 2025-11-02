<?php
try {
    $host = 'localhost';
    $dbname = 'guvi_users';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check what photos exist in uploads folder
    $uploadDir = __DIR__ . '/uploads/';
    $files = scandir($uploadDir);
    
    echo "<h3>Available photos in uploads folder:</h3>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..' && (strpos($file, '.png') !== false || strpos($file, '.jpg') !== false)) {
            echo $file . "<br>";
        }
    }
    
    // Update user 1 with an existing photo (if any)
    $existingPhotos = array_filter($files, function($file) {
        return $file != '.' && $file != '..' && (strpos($file, '.png') !== false || strpos($file, '.jpg') !== false);
    });
    
    if (!empty($existingPhotos)) {
        $firstPhoto = reset($existingPhotos);
        $stmt = $pdo->prepare("UPDATE guvi1users SET photo = ? WHERE id = 1");
        $stmt->execute(["uploads/$firstPhoto"]);
        echo "<br><br>Updated user 1 photo to: uploads/$firstPhoto";
    } else {
        echo "<br><br>No photos found in uploads folder";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>