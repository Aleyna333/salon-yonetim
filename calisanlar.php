<?php
require "db.php";

function guvenli($veri) {
  return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");  
}

function bosMu($veri) {
    return empty(trim($veri));
}
 
if(isset($_POST["edit_id"]) && ($_POST["edit_id"]) != "") {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = $_POST["phone"];
    $edit_id = $_POST["edit_id"];

    if(
    bosMu($name)||
    bosMu($surname)||
    bosMu($phone)
) {
    echo "bu alanlar dolu olmalı";
} else if (!is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 11){
        echo "Telefon sadece sayı içermeli ve 10-11 haneli olmalı";
        } else {

    $stmt = mysqli_prepare($baglanti, "UPDATE calisanlar SET
    name = ?,
    surname = ?,
    phone = ?
    WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $surname, $phone, $edit_id);
    mysqli_stmt_execute($stmt);
    header("Location:calisanlar.php");
    exit;
}
}
    
 else if(isset($_POST["name"])) {
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $phone = $_POST["phone"];

if(
    bosMu($name)||
    bosMu($surname)||
    bosMu($phone)
) {
    echo "bu alanlar dolu olmalı";
} else if (!is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 11){
        echo "Telefon sadece sayı içermeli ve 10-11 haneli olmalı";
        } else {
            $stmt = mysqli_prepare($baglanti, "INSERT INTO calisanlar (name, surname, phone) VALUES(?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $name, $surname, $phone);
            mysqli_stmt_execute($stmt);
            header("Location:calisanlar.php");
            exit;
        }
    }

    if(isset($_POST["sil_id"])){
       $sil_id = $_POST["sil_id"];
       $stmt = mysqli_prepare($baglanti, "DELETE from calisanlar WHERE id = ?");
       mysqli_stmt_bind_param($stmt, "i", $sil_id);
       mysqli_stmt_execute($stmt);
       header("Location:calisanlar.php");
       exit;
    }

     if(isset($_GET["edit_id"])){
        $edit_id = $_GET["edit_id"];
       $stmt = mysqli_prepare($baglanti, "SELECT * from calisanlar WHERE id = ?");
       mysqli_stmt_bind_param($stmt, "i", $edit_id);
       mysqli_stmt_execute($stmt); 
       $sonuc = mysqli_stmt_get_result($stmt);
       $editRow = mysqli_fetch_assoc($sonuc);
     }

     $stmt = mysqli_prepare($baglanti, "SELECT * from calisanlar");
     mysqli_stmt_execute($stmt); 
     $sonuc = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışanlar</title>
</head>
<body>

    <form method="post">
        <input type="hidden" name="edit_id" value="<?php if(isset($editRow)) {
            echo guvenli($editRow["id"]);
        } else{echo "";} ?>">

         <input type="text" name="name" placeholder="Ad" value="<?php if(isset($editRow)) {
            echo guvenli($editRow["name"]);
        } else{echo "";} ?>">

         <input type="text" name="surname" placeholder="Soyad" value="<?php if(isset($editRow)) {
            echo guvenli($editRow["surname"]);
        } else{echo "";} ?>">

         <input type="text" name="phone" placeholder="Telefon" value="<?php if(isset($editRow)) {
            echo guvenli($editRow["phone"]);
        } else{echo "";} ?>">

        <button type="submit">Kaydet</button>
    </form>

    <table>
        <tr>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Telefon</th>
            <th>Düzenle</th>
            <th>Sil</th>
        </tr>

    <?php    
    while($row = mysqli_fetch_assoc($sonuc)) { ?>
    <tr>
    <td><?= guvenli($row["name"]) ?></td>
    <td><?= guvenli($row["surname"]) ?></td>
    <td><?= guvenli($row["phone"]) ?></td>
    <td>
        <form method="Get">
            <input type="hidden" name="edit_id" value="<?= guvenli($row["id"]) ?>">
            <button type="submit">Düzenle</button>
        </form>
    </td>
    <td>
        <form method="post">
            <input type="hidden" name="sil_id" value="<?= guvenli($row["id"]) ?>">
            <button type="submit" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
        </form>
    </td>
    </tr>
    <?php
    } 
    ?>

    </table>
</body>
</html>
