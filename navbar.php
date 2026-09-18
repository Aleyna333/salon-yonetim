<div class="col-2 bg-light" style="min-height: 100vh;">
    <div class="list-group">
        <a href="index.php" class="list-group-item list-group-item-action <?php if(strpos($_SERVER["PHP_SELF"], "index.php")) echo "active"; ?>">Anasayfa</a>
        <a href="musteriler.php" class="list-group-item list-group-item-action mb-2 <?php if(strpos($_SERVER["PHP_SELF"], "musteriler.php")) echo "active"; ?>">Müşteriler</a>
        <a href="calisanlar.php" class="list-group-item list-group-item-action mb-2 <?php if(strpos($_SERVER["PHP_SELF"], "calisanlar.php")) echo "active"; ?>">Çalışanlar</a>
        <a href="hizmetler.php" class="list-group-item list-group-item-action mb-2 <?php if(strpos($_SERVER["PHP_SELF"], "hizmetler.php")) echo "active"; ?>">hizmetler</a>
        <a href="randevular.php" class="list-group-item list-group-item-action mb-2 <?php if(strpos($_SERVER["PHP_SELF"], "randevular.php")) echo "active"; ?>">randevular</a>
    </div>
</div>