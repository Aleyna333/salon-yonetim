<?php
require "db.php";

function guvenli($veri) {
    return htmlspecialchars($veri, ENT_QUOTES, "UTF-8");
}

function bosMu($veri) {
    return empty(trim($veri));
}

$stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as toplam_musteri from musteriler ");
     mysqli_stmt_execute($stmt); 
     $sonuc = mysqli_stmt_get_result($stmt);
     $toplam_musteri = mysqli_fetch_assoc($sonuc);

     $stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as toplam_calisan from calisanlar");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     $toplam_calisan = mysqli_fetch_assoc($sonuc);

    $stmt = mysqli_prepare($baglanti, "SELECT COUNT(*) as toplam_hizmet from hizmetler");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     $toplam_hizmet = mysqli_fetch_assoc($sonuc);

     
    $stmt = mysqli_prepare($baglanti, "SELECT COUNT(*)  as bugunku_randevu from randevular WHERE DATE(tarih) = DATE(NOW())");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     $bugunku_randevu = mysqli_fetch_assoc($sonuc);

     
    $stmt = mysqli_prepare($baglanti, "SELECT musteriler.name as musteri_adi, hizmetler.name as hizmet_adi, randevular.tarih as randevu_tarihi
    FROM randevular JOIN musteriler ON randevular.musteri_id = musteriler.id 
    JOIN hizmetler ON randevular.hizmet_id = hizmetler.id WHERE DATE(randevular.tarih) = DATE(NOW())");
     mysqli_stmt_execute($stmt);
     $sonuc = mysqli_stmt_get_result($stmt);
     
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Salon Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


<div class="row g-3">

<?php include "navbar.php"; ?>

<div class="col-10 p-3">
<div class="row g-3">

    <div class="col-7">
        <div class="row">
             <div class="col-6">
    <div class="card shadow-sm text-center border-primary">
        <div class="card-body">
            <h5>Toplam Müşteri</h5>
            <p class="fs-3 fw-bold text-primary"> <?php echo (guvenli($toplam_musteri["toplam_musteri"]));?></p>
        </div>
    </div>
    </div>

    <div class="col-6">
    <div class="card shadow-sm text-center border-success">
        <div class="card-body">
            <h5>Toplam Çalışan</h5>
            <p class="fs-3 fw-bold text-success"><?php echo(guvenli($toplam_calisan["toplam_calisan"])); ?></p>
        </div>
    </div>
    </div>

    
<div class=" col-12 row g-3 mt-3">
<div class="col-6">
    <div class="card shadow-sm text-center border-warning">
        <div class="card-body">
            <h5>Toplam Hizmet</h5>
            <p class="fs-3 fw-bold text-warning"><?php echo(guvenli($toplam_hizmet["toplam_hizmet"])); ?></p>
        </div>
    </div>
    </div>

    <div class="col-6">
    <div class="card shadow-sm text-center border-info">
        <div class="card-body">
            <h5>Bugünkü Randevu</h5>
            <p class="fs-3 fw-bold text-info"><?php echo(guvenli($bugunku_randevu["bugunku_randevu"])); ?></p>
        </div>
    </div>
    </div>
        </div>

    
</div>
    </div>

        <div class="col-5">

            <h5>Bugünkü Randevular</h5>

            <table class="table table-bordered table-hover">
                <tr>
                    <th>Müşteri</th>
                    <th>Hizmet</th>
                    <th>Tarih</th>
                </tr>
                <?php   
                if(mysqli_num_rows($sonuc)==0){
                    echo "<tr><td colspan='3'>Bugün için randevu yok</td></tr>";
                } else {
                    while($row = mysqli_fetch_assoc($sonuc)) {
                    ?>
                    <tr>
                        <td><?php echo guvenli($row["musteri_adi"]); ?></td>
                        <td><?php echo guvenli($row["hizmet_adi"]); ?></td>
                        <td><?php echo guvenli($row["randevu_tarihi"]); ?></td>
                    </tr>
                    <?php
                    }
                }
                ?>
            </table>
        </div>
 
    </div>
</div>
</div>
</body>
</html>