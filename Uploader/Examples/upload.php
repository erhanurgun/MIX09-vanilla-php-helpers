<?php

// Dosya yükleme sınıfını dahil edin
require_once "../FileUploader.php";
use Uploader\FileUploader;

// Yükleme işlemini gerçekleştirecek nesneyi oluşturun
$uploader = new FileUploader();

if (isset($_FILES['files'])) {
    // Yüklenecek dosya ve özelliklerini belirleyin
    $files = $_FILES['files'];
    $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
    $maxSize = 1000000; // 1 MB
    $uploadPath = "uploads/";

    // Dosya yükleme işlemini gerçekleştirin
    if ($uploader->upload($files, $allowedTypes, $maxSize, $uploadPath)) {
        echo "Dosya başarıyla yüklendi!";
    } else {
        // Hata mesajlarını gösterin
        $errors = $uploader->getErrors();
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}
