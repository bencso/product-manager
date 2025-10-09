<?php
require "../header.php";
?>
<link rel="stylesheet" href="../styles/index.styles.css">
<link rel="stylesheet" href="../styles/termekek.styles.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<title>Termékek</title>
</head>

<body>
    <button id="logoutBtn">Kijelentkezés
        <span class="material-symbols-outlined">
            logout
        </span>
    </button>
    <h1>Termékek</h1>
    <div class="tableContainer">
        <table id="itemsTable">
            <tr class="none">
                <th colspan="4">
                    <div class="inputContainer">
                        <span class="material-symbols-outlined">
                            search
                        </span>
                        <input id="searchInput" />
                        <button id="generatePdfBtn">
                            <span class="material-symbols-outlined">
                                contract
                            </span>
                        </button>
                    </div>
                </th>
            </tr>
            <tr>
                <th class="sort" id="cikkszamOszlop" data-sorrend="0">Cikkszám</th>
                <th class="sort" id="megnevezesOszlop" data-sorrend="0">Cikk megnevezése</th>
                <th class="sort" id="nettoarOszlop" data-sorrend="0">Nettó ár</th>
                <th class="sort" id="afaOszlop" data-sorrend="0">ÁFA (%)</th>
            </tr>
        </table>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/3.0.3/jspdf.umd.min.js" integrity="sha512-+EeCylkt9WHJk5tGJxYdecHOcXFRME7qnbsfeMsdQL6NUPYm2+uGFmyleEqsmVoap/f3dN/sc3BX9t9kHXkHHg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="script.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("generatePdfBtn").addEventListener("click", () => {
            generatePDF();
        })
    });

    function generatePDF() {
        const table = document.getElementById('itemsTable');
        const pdf = new window.jspdf.jsPDF();
        const search = document.getElementById("searchInput").value;
        const searchLabe = `, keresés: ${document.getElementById('searchInput').value}`;
        pdf.text(10, 10, document.getElementById('searchInput').value ? "Termékek " + searchLabe : "Termékek");
        let yPos = 20;
        datas.forEach(data => {
            if (!search || data.cikkszam.toLowerCase().includes(search.toLowerCase()) || data.megnevezes.toLowerCase().includes(search.toLowerCase()) || data.nettoAr.toString().includes(search)) {
                pdf.text(10, yPos, `${data.cikkszam} ${data.megnevezes} ${data.nettoAr} Ft  ${data.afa}%`);
                yPos += 10;
            }
        });
        pdf.save(`termekek_${new Date().toLocaleString("hu-hu")}.pdf`);
    }
</script>

</html>