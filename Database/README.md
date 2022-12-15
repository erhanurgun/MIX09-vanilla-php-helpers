
# PDO-Database-Management

PDO ile veritabanı yönetimi

## Ayarlar

1. config.php dosyasını kendinize göre düzenleyin
```php
<?php
define("DB_HOST", "localhost");
define("DB_NAME", "jiyodb");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_DRIVER", "mysql");
define("DB_CHARSET", "utf8");
```

## Kullanım

1. JiyoDB.php dosyasını dahil edin
2. JiyoDB sınıfından bir nesne oluşturun    
3. Nesnenin içerisindeki fonksiyonları kullanın

## Örnek

```php
<?php

require_once 'JiyoDB.php';            // JiyoDB.php sınıfını sayfaya dahil edildi

// kullanımı
$db = new JiyoDB();                   // JiyoDB sınıfından bir nesne oluşturuldu

// ekleme işlemi
$data = [                                   // eklenecek veriler
    'name' => 'Erhan RGN',                  
    'email' => 'urgun.js@gmail.com'         
];
$db->insert('users', $data);                // users tablosuna $data verileri eklenecek

// güncelleme işlemi
$data = [                                   // güncellenecek veriler
    'name' => 'Erhan ÜRGÜN',                
    'email' => 'erhan.rgn04@gmail.com'      
];
$condition = "id=1";
$db->update('users', $data, $condition);    // id'si 1 olan kullanıcı güncellenecek

// silme işlemi
$condition = "id=1";
$db->delete('users', $condition);           // users tablosundan id'si 1 olan kayıt silinecek

// seçme işlemi
$users = $db->select('users');              // users tablosundaki tüm verileri seç
foreach ($users as $user) {                 // döngü ile kullanıcılar listelendi
    echo "$user->name $user->email <br>";   // Erhan ÜRGÜN
    // echo $user['name'] . ' - ' . $user['email'] . '<br>';
}

// seçme işlemi (join)
$users = $db->select('users', 'users.name, posts.title', 'posts', 'users.id=posts.user_id'); 

// seçme işlemi (order)
$users = $db->select('users', '*', NULL, NULL, 'name DESC');

// seçme işlemi (limit)
$users = $db->select('users', '*', NULL, NULL, NULL, '2');

// seçme işlemi (join, order, limit)
$users = $db->select('users', 'users.name, users.email, posts.title', 'posts', 'users.id=posts.user_id', 'users.name DESC', '2');

// veritabanı oluşturma
$db->createDatabase('test');

// veritabanı silme
$db->dropDatabase('test');

// tablo oluşturma
$columns = [
    'id' => 'INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY',
    'name' => 'VARCHAR(255) NOT NULL',
    'email' => 'VARCHAR(255) NOT NULL'
];
$db->createTable('users', $columns);

// tablo silme
$db->dropTable('users');

// tablo içerisindeki verileri silmek
$db->truncateTable('users');

?>
```
