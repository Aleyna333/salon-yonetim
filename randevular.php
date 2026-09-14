<?php
require  "db.php";

function guvenli($veri) {
    return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMU($veri){
    return empty(trim($veri));
}

$stmt = mysqli_prepare($baglanti, "SELECT * from musteriler");
mysqli_stmt_execute($stmt);
$musteri_sonuc = mysqli_stmt_get_result($stmt);


$stmt = mysqli_prepare($baglanti, "SELECT * from hizmetler");
mysqli_stmt_execute($stmt);
$hizmet_sonuc = mysqli_stmt_get_result($stmt);


if(isset($_POST["edit_id"]) && $_POST["edit_id"] != "") {
    $musteri_id = $_POST["musteri_id"];
    $hizmet_id = $_POST["hizmet_id"];
    $tarih = $_POST["tarih"];
    $edit_id = $_POST["edit_id"];

    if( 
    bosMU($musteri_id)||
    bosMU($hizmet_id)||
    bosMU($tarih)
    ){
        echo"Bu alanlar dolu olmalı!";
    }else{
    $stmt = mysqli_prepare($baglanti, "UPDATE randevular SET 
    musteri_id = ?, 
    hizmet_id = ?,
    tarih = ?
    WHERE id = ?
    ");
    mysqli_stmt_bind_param($stmt, "iisi", $musteri_id, $hizmet_id, $tarih, $edit_id);
    mysqli_stmt_execute($stmt);
    header("location:randevular.php");
    exit;
    }

}else if(isset($_POST["musteri_id"])){
    $hizmet_id = $_POST["hizmet_id"];
    $tarih = $_POST["tarih"];
    $musteri_id = $_POST["musteri_id"];

    if(
    bosMU($musteri_id)||
    bosMU($hizmet_id)||
    bosMU($tarih)
    ){
        echo"Bu alanlar dolu olmalı!";
    }else{
    $stmt = mysqli_prepare($baglanti, "INSERT INTO randevular(musteri_id, hizmet_id, tarih) VALUES(?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "iis", $musteri_id, $hizmet_id, $tarih);
    mysqli_stmt_execute($stmt);
    header("location:randevular.php");
    exit;
    }
}

    $stmt = mysqli_prepare($baglanti, "SELECT musteriler.name as musteri_adi, musteriler.surname as musteri_soyadı, hizmetler.name as hizmet_adi, randevular.tarih as randevu_tarihi, randevular.id as randevu_id from randevular 
    JOIN musteriler ON randevular.musteri_id = musteriler.id JOIN hizmetler ON randevular.hizmet_id = hizmetler.id ");
    mysqli_stmt_execute($stmt);
    $sonuc = mysqli_stmt_get_result($stmt);

    if(isset($_GET["edit_id"])){
        $edit_id = $_GET["edit_id"];
        $stmt =  mysqli_prepare($baglanti, "SELECT * from randevular WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $edit_sonuc = mysqli_stmt_get_result($stmt);
        $editRow = mysqli_fetch_assoc($edit_sonuc);
    }

    if(isset($_POST["sil_id"])) {
    $sil_id = $_POST["sil_id"];
    $stmt = mysqli_prepare($baglanti, "DELETE from randevular WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $sil_id);
    mysqli_stmt_execute($stmt);
    header("location:randevular.php");
    exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="post">
        <input type="hidden" name="edit_id" value="<?php if(isset($editRow)) {
    echo guvenli($editRow["id"]);
    } else{echo"";} ?>">

 <select name="musteri_id">

        <?php while($row = mysqli_fetch_assoc($musteri_sonuc)){ ?>
        <option value="<?= guvenli($row["id"]) ?>" <?php if(isset($editRow) && $editRow["musteri_id"] == $row["id"])
            echo "selected"; ?>><?= guvenli($row["name"]) . " " . guvenli($row["surname"])?></option>

        <?php } ?>
    </select>

        <select name="hizmet_id">
        <?php while($row = mysqli_fetch_assoc($hizmet_sonuc)){ ?>
        <option value="<?= guvenli($row["id"]) ?>" <?php if(isset($editRow) && $editRow["hizmet_id"] == $row["id"])         
            echo "selected"; ?>><?= guvenli($row["name"]) ?></option>

        <?php } ?>
    </select>

    <input type="date" name="tarih"  value="<?php if(isset($editRow)) {
    echo guvenli($editRow["tarih"]);
    } else{echo"";} ?>">

    <button type="submit">Kaydet</button>

    </form>   
        <table>
        <tr>
            <th>Müşteri Adi/Soyadı</th>
            <th>Hizmet Adi</th>
            <th>Randevu Tarihi</th>
            <th>Düzenle</th>
            <th>Sil</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($sonuc)){ ?>
        <tr>
            <td><?php echo guvenli($row["musteri_adi"]);?></td> <td><?php echo guvenli($row["musteri_soyadı"]);?></td>
            <td><?php echo guvenli($row["hizmet_adi"]);?></td>
            <td><?php echo guvenli($row["randevu_tarihi"]);?></td>
            <td> 
                <form method="get">
                    <input type="hidden" name="edit_id" value="<?= guvenli($row["randevu_id"])?>">
                    <button type="submit">Düzenle</button>
                </form>
            </td>
            <td>
                <form method="post">
                    <input type="hidden" name="sil_id" value="<?= guvenli($row["randevu_id"] )?>">
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