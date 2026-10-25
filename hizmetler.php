<?php
require "auth.php";
require "db.php";

function guvenli($veri){
   return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMu($veri){
    return empty(trim($veri));
}

if(isset($_POST["edit_id"]) && ($_POST["edit_id"]) != "") {
$name = $_POST["name"];
$ucret = $_POST["ucret"];
$edit_id = $_POST["edit_id"];

if( 
    bosMu($name)||
    bosMu($ucret)
){
    echo"Bu alanlar dolu olmalı!";
} else{
$stmt = mysqli_prepare($baglanti, "UPDATE hizmetler SET
name = ?,
ucret = ?
WHERE id = ?
");
mysqli_stmt_bind_param($stmt, "sdi", $name, $ucret, $edit_id);
mysqli_stmt_execute($stmt);
header("location:hizmetler.php");
exit;
}
}

 elseif(isset($_POST["name"])){
$name = $_POST["name"];
$ucret = $_POST["ucret"];

if( 
    bosMu($name)||
    bosMu($ucret)
){
    echo"Bu alanlar dolu olmalı!";
} else {
$stmt = mysqli_prepare($baglanti, "INSERT INTO hizmetler(name, ucret) VALUES(?, ?)");
mysqli_stmt_bind_param($stmt, "sd", $name, $ucret);
mysqli_stmt_execute($stmt);
header("location:hizmetler.php");
exit;
}
}


if(isset($_GET["edit_id"])){
$edit_id = $_GET["edit_id"];
$stmt = mysqli_prepare($baglanti, "SELECT * from hizmetler WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $edit_id);
mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);
$editRow = mysqli_fetch_assoc($sonuc);
}


if(isset($_POST["sil_id"])){
$sil_id = $_POST["sil_id"];
$stmt = mysqli_prepare($baglanti, "DELETE from hizmetler WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $sil_id);
mysqli_stmt_execute($stmt);
header("location:hizmetler.php");
exit;
}

$stmt = mysqli_prepare($baglanti, "SELECT * from hizmetler");
mysqli_stmt_execute($stmt);
$sonuc = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hizmetler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
<div class="row">

<?php include "navbar.php"; ?>

<main class="col-12 col-md-10 p-3">
    <h4 class="mb-3">Hizmetler</h4>

    <form method="post" class="row g-2 mb-4">
        <input type="hidden" name="edit_id" value="<?= isset($editRow) ? guvenli($editRow["id"]) : "" ?>">

        <div class="col-12 col-md-6">
            <input type="text" class="form-control" name="name" placeholder="Hizmet"
                   value="<?= isset($editRow) ? guvenli($editRow["name"]) : "" ?>">
        </div>
        <div class="col-12 col-md-4">
            <input type="number" step="0.01" min="0" class="form-control" name="ucret" placeholder="Ücret (₺)"
                   value="<?= isset($editRow) ? guvenli($editRow["ucret"]) : "" ?>">
        </div>
        <div class="col-12 col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary flex-fill">Kaydet</button>
            <?php if (isset($editRow)) { ?>
                <a href="hizmetler.php" class="btn btn-secondary">İptal</a>
            <?php } ?>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover align-middle bg-white border">
            <thead class="table-light">
                <tr>
                    <th>Hizmet</th>
                    <th>Ücret</th>
                    <th style="width:1%">İşlemler</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($sonuc)) { ?>
                <tr>
                    <td><?= guvenli($row["name"]) ?></td>
                    <td><?= number_format($row["ucret"], 2, ",", ".") ?> ₺</td>
                    <td>
                        <div class="d-flex gap-1">
                            <form method="get">
                                <input type="hidden" name="edit_id" value="<?= guvenli($row["id"]) ?>">
                                <button type="submit" class="btn btn-warning btn-sm">Düzenle</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="sil_id" value="<?= guvenli($row["id"]) ?>">
                                <button type="submit" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
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