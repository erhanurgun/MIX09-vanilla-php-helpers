<?php

namespace Uploader;

class FileUploader
{
    // Hata mesajlarını tutacak bir dizi oluşturun
    private $errors = [];

    // Dosya yükleme fonksiyonu
    public function upload($file, $allowedTypes, $maxSize = 1000000, $uploadPath = "uploads/", $UniqName = true)
    {
        // Dosya dizisi boş ise hata mesajı döndürün
        if (empty($file['name']) || $file['error'] == 4) {
            $this->errors[] = "Yüklenecek herhangi bir dosya bulunamadı!";
            return false;
        }

        // Yüklenen dosyanın türünü kontrol edin
        if (!in_array($file['type'], $allowedTypes)) {
            $typeArr = implode(", ", $allowedTypes);
            $this->errors[] = "Geçersiz dosya türü tesbit edildi, sadece \"{$typeArr}\" tipinde ki dosyalar yüklenebilir!";
            return false;
        }

        // Yüklenen dosyanın boyutunu kontrol edin
        if ($file['size'] > $maxSize) {
            $maxSize = $maxSize / 1000000;
            $this->errors[] = "Dosya boyutu çok büyük, en fazla {$maxSize} MB boyutunda dosyalar yüklenebilir!";
            return false;
        }

        // Dosyayı geçici dizine taşıyın
        $tmpFile = $file['tmp_name'];

        // Dosya adını oluşturun
        if ($UniqName) {
            $fileName = time() . '-' . uniqid() . '-' . $file['name'];
        } else {
            $fileName = $file['name'];
        }

        // uploadPath dizini yok ise oluşturun
        if (!@file_exists($uploadPath)) {
            @mkdir($uploadPath, 0777, true);
        }

        // Dosyayı kalıcı bir dizine taşıyın
        $destination = $uploadPath . $fileName;

        // Dosya adı daha öncesinde mevcut ise dosyayı silerek yükleyin
        if (@file_exists($destination)) {
            @unlink($destination);
        }

        if (@move_uploaded_file($tmpFile, $destination)) {
            return true;
        } else {
            $this->errors[] = "Dosya yükleme hatası oluştu, lütfen tekrar deneyin!";
            return false;
        }
    }

    // Hata mesajlarını döndüren bir fonksiyon oluşturun
    public function getErrors()
    {
        return $this->errors;
    }
}
