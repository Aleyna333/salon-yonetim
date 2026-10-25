<?php
require "auth.php";
require "db.php";

function guvenli($veri) {
  return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");  
}

function bosMu($veri) {
    return empty(trim($veri));
}

function telefonDuzelt($tel) {
    return preg_replace('/\D/', '', $tel);
}

function telefonGecerliMi($tel) {
    return strlen($tel) == 11 && substr($tel, 0, 2) == "05";
}
 
if(isset($_POST["edit_id"]) && ($_POST["edit_id"]) != "") {
    $name = $_POST["name"];
    $surname = $_POST["surname"];
    $phone = telefonDuzelt($_POST["phone"]);
    $edit_id = $_POST["edit_id"];

    if(
    bosMu($name)||
    bosMu($surname)||
    bosMu($phone)
) {
    echo "bu alanlar dolu olmalı";
} else if (!telefonGecerliMi($phone)) {
    echo "Telefon 05 ile başlayan 11 haneli olmalı (örn: 05321234567)";
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
        $phone = telefonDuzelt($_POST["phone"]);

if(
    bosMu($name)||
    bosMu($surname)||
    bosMu($phone)
) {
    echo "bu alanlar dolu olmalı";
} else if (!telefonGecerliMi($phone)) {
    echo "Telefon 05 ile başlayan 11 haneli olmalı (örn: 05321234567)";
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
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Çalışanlar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
<div class="row">
<?php include "navbar.php"; ?>

<main class="col-12 col-md-10 p-3">
    <h4 class="mb-3">Çalışanlar</h4>

    <form method="post" class="row g-2 mb-4">
        <input type="hidden" name="edit_id" value="<?= isset($editRow) ? guvenli($editRow["id"]) : "" ?>">

        <div class="col-12 col-md-3">
            <input type="text" class="form-control" name="name" placeholder="Ad" value="<?= isset($editRow) ? guvenli($editRow["name"]) : "" ?>">
        </div>
        <div class="col-12 col-md-3">
            <input type="text" class="form-control" name="surname" placeholder="Soyad" value="<?= isset($editRow) ? guvenli($editRow["surname"]) : "" ?>">
        </div>
        <div class="col-12 col-md-3">
            <input type="tel" class="form-control" name="phone" placeholder="05XX XXX XX XX" value="<?= isset($editRow) ? guvenli($editRow["phone"]) : "" ?>">
        </div>
        <div class="col-12 col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary flex-fill">Kaydet</button>
            <?php if(isset($editRow)) { ?>
                <a href="calisanlar.php" class="btn btn-secondary">İptal</a>
            <?php } ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle bg-white border">
            <thead class="table-light">
                <tr><th>Ad</th><th>Soyad</th><th>Telefon</th><th style="width:1%">İşlemler</th></tr>
            </thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($sonuc)) { ?>
                <tr>
                    <td><?= guvenli($row["name"]) ?></td>
                    <td><?= guvenli($row["surname"]) ?></td>
                    <td><?= guvenli($row["phone"]) ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="get">
                                <input type="hidden" name="edit_id" value="<?= guvenli($row["id"]) ?>">
                                <button type="submit" class="btn btn-warning btn-sm">Düzenle</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="sil_id" value="<?= guvenli($row["id"]) ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</main>

</div>
</div>
</body>
</html>