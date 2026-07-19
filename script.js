
    document.getElementById("yeniBtn").onclick = function() {
         document.getElementById("modal").style.display = "block";
    };

     document.getElementById("kapatBtn").onclick = function() {
         document.getElementById("modal").style.display = "none";
         window.location.href = "musteriler.php";
    };
