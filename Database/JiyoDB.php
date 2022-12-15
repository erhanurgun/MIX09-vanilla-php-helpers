<?php

namespace Database;

// JiyoDB adında bir sınıf oluştur ve PDO'dan miras al
class JiyoDB extends \PDO
{
    protected $dbName;                  // Veritabanı adı
    protected $dbUser;                  // Veritabanı kullanıcı adı
    protected $dbPass;                  // Veritabanı şifresi
    protected $dbHost;                  // Veritabanı sunucusu
    protected $dbCharset;               // Veritabanı karakter seti
    protected $dbDriver;                // Veritabanı sürücüsü

    // İlk başta çalışacak olan fonksiyon
    public function __construct()
    {
        require_once '../config/jiyodb.php';    // config.php dosyasını dahil et
        $this->dbName = DB_NAME;                // Veritabanı adı
        $this->dbUser = DB_USER;                // Veritabanı kullanıcı adı
        $this->dbPass = DB_PASS;                // Veritabanı şifresi
        $this->dbHost = DB_HOST;                // Veritabanı sunucusu
        $this->dbCharset = DB_CHARSET;          // Veritabanı karakter seti
        $this->dbDriver = DB_DRIVER;            // Veritabanı sürücüsü

        // Veritabanı bağlantısı için gerekli olan bilgileri birleştir
        $dsn = $this->dbDriver . ':host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=' . $this->dbCharset;

        // Hata yakalamak için try-catch bloğu
        try {                           
            // PDO sınıfının __construct fonksiyonunu çağır ve veritabanı bağlantısı için gerekli olan bilgileri gönder 
            parent::__construct($dsn, $this->dbUser, $this->dbPass);    
            // Hata modunu ayarla
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {    // Hata yakalandığında $e değişkenine atar
            echo $e->getMessage();      // Hata mesajını ekrana yazdırır
        }
    }

    // Veritabanına veri eklemek için kullanılır
    public function insert($table, $data)               
    {
        $keys = implode(',', array_keys($data));                    // Veritabanı tablosundaki sütunları alır
        $values = ':' . implode(', :', array_keys($data));          // Veritabanı tablosundaki sütunların değerlerini alır
        $sql = "INSERT INTO $table ($keys) VALUES ($values)";       // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        foreach ($data as $key => $value) {                         // Döngü ile verileri al
            $stmt->bindValue(":$key", $value);                      // Verileri bağla
        }                                       
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $this->lastInsertId();                               // Son eklenen verinin id'sini döndür
    }

    // Veritabanına veri güncellemek için kullanılır
    public function update($table, $data, $condition)
    { 
        $updateKeys = NULL;                                         // Güncellenecek verileri tutacak değişken
        foreach ($data as $key => $value) {                         // Döngü ile verileri al
            $updateKeys .= "$key=:$key,";                           // Güncellenecek verileri al
        }
        $updateKeys = rtrim($updateKeys, ',');                      // Sonundaki virgülü sil
        $sql = "UPDATE $table SET $updateKeys WHERE $condition";    // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        foreach ($data as $key => $value) {                         // Döngü ile verileri al
            $stmt->bindValue(":$key", $value);                      // Verileri bağla
        }
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->rowCount();                                   // Güncellenen veri sayısını döndür
    }

    //
    public function delete($table, $condition, $limit = 1)
    {
        $sql = "DELETE FROM $table WHERE $condition LIMIT $limit";  // SQL sorgusu
        return $this->exec($sql);                                   // SQL sorgusunu çalıştır
    }

    // Veritabanından veri çekmek için kullanılır
    public function select($table, $rows = '*', $join = NULL, $condition = NULL, $order = NULL, $limit = NULL)
    {
        $sql = "SELECT $rows FROM $table";                          // SQL sorgusu
        if ($join != NULL) {                                        // Eğer join varsa
            $sql .= " JOIN $join";                                  // SQL sorgusuna join ekle
        }
        if ($condition != NULL) {                                   // Eğer koşul varsa
            $sql .= " WHERE $condition";                            // SQL sorgusuna koşul ekle
        }
        if ($order != NULL) {                                       // Eğer sıralama varsa
            $sql .= " ORDER BY $order";                             // SQL sorgusuna sıralama ekle
        }
        if ($limit != NULL) {                                       // Eğer limit varsa
            $sql .= " LIMIT $limit";                                // SQL sorgusuna limit ekle
        }
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);                  // Verileri döndür
    }
    
    // veritabanı oluşturmak için kullanılır
    public function createDatabase($databaseName)
    {
        $sql = "CREATE DATABASE $databaseName";                     // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->rowCount();                                   // Oluşturulan veritabanı sayısını döndür
    }

    // veritabanı silmek için kullanılır
    public function dropDatabase($databaseName)
    {
        $sql = "DROP DATABASE $databaseName";                       // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->rowCount();                                   // Silinen veritabanı sayısını döndür
    }

    // veritabanı tablosu oluşturmak için kullanılır
    public function createTable($tableName, $data)
    {
        $sql = "CREATE TABLE $tableName ($data)";                   // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->rowCount();                                   // Oluşturulan tablo sayısını döndür
    }

    // veritabanı tablosu silmek için kullanılır
    public function dropTable($tableName)
    {
        $sql = "DROP TABLE $tableName";                             // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->rowCount();                                   // Silinen tablo sayısını döndür
    }

    // veritabanı tablosu içerisindeki verileri silmek için kullanılır
    public function truncateTable($tableName)
    {
        $sql = "TRUNCATE TABLE $tableName";                         // SQL sorgusu
        $stmt = $this->prepare($sql);                               // SQL sorgusunu hazırla
        $stmt->execute();                                           // SQL sorgusunu çalıştır
        return $stmt->rowCount();                                   // Silinen veri sayısını döndür
    }
}