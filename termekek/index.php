<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termékek</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 50%;
            user-select: none;
        }

        td,
        th {
            border: 1px solid #000000ff;
            text-align: left;
            padding: 8px;

        }

        tr:not(.none) th:hover {
            background-color: rgba(128, 128, 128, 1);
            cursor: n-resize;
        }


        tr:nth-child(even) {
            background-color: #dddddd;
        }

        th {
            padding: 12px 24px;
            text-align: center;
        }

        th input {
            width: 100%;
            height: 100%;
            border: 1px solid rgba(128, 128, 128, 1);
        }

        .searched {
            font-weight: bold;
        }
    </style>
    </style>
</head>

<body>
    <button id="logoutBtn" oncl>Kijelentkezés</button>
    <h1>Termékek</h1>
    <table id="itemsTable">
        <tr class="none">
            <th>Keresés:</th>
            <th colspan="3">
                <input id="searchInput" />
            </th>
        </tr>
        <tr>
            <th>Cikkszám</th>
            <th>Cikk megnevezése</th>
            <th>Nettó ár</th>
            <th>ÁFA (%)</th>
        </tr>

    </table>
</body>
<script>
    const datas = [{
            cikkszam: "123TTA45678",
            megnevezes: "Laptop",
            nettoAr: 250000,
            afa: 27
        },
        {
            cikkszam: "23GA456789",
            megnevezes: "Monitor",
            nettoAr: 75000,
            afa: 27
        },
        {
            cikkszam: "34567890",
            megnevezes: "Egér",
            nettoAr: 8000,
            afa: 27
        },
        {
            cikkszam: "456789fa01",
            megnevezes: "Billentyűzet",
            nettoAr: 15000,
            afa: 27
        },
        {
            cikkszam: "56789BTG012",
            megnevezes: "Webkamera",
            nettoAr: 12000,
            afa: 27
        },
        {
            cikkszam: "67890123",
            megnevezes: "Headset",
            nettoAr: 18000,
            afa: 27
        },
        {
            cikkszam: "7890vbar234",
            megnevezes: "SSD",
            nettoAr: 25000,
            afa: 27
        },
        {
            cikkszam: "89012345",
            megnevezes: "RAM",
            nettoAr: 22000,
            afa: 27
        }
    ];

    document.addEventListener("DOMContentLoaded", () => {
        fetch("../api/valid.php", {
                method: "POST",
                credentials: "include"
            })
            .then((res) => res.json())
            .then((data) => {
                if (data.status !== 200) {
                    document.cookie = 'token=; Max-Age=0; path=/; domain=' + location.host;
                    window.location.href = "/phpfeladat";
                } else {
                    loadTable(null);

                    document.getElementById("logoutBtn").addEventListener("click", () => {
                        document.cookie = 'token=; Max-Age=0; path=/; domain=' + location.host;
                        window.location.href = "/phpfeladat";
                    });

                    document.getElementById("searchInput").addEventListener("input", (e) => {
                        loadTable(e.target.value);
                    })
                }
            })
            .catch(err => {
                document.cookie = 'token=; Max-Age=0; path=/; domain=' + location.host;
                window.location.href = "/phpfeladat";
            });
    });

    function loadTable(search) {
        const rows = document.getElementById("itemsTable").getElementsByTagName("tr");
        while (rows.length > 2) {
            rows[2].remove();
        }

        datas.forEach(data => {
            if (!search || data.cikkszam.toLowerCase().includes(search.toLowerCase()) || data.megnevezes.toLowerCase().includes(search.toLowerCase()) || data.nettoAr.toString().includes(search)) {
                const tableRow = document.createElement("tr");
                let cikkszam = data.cikkszam;
                let megnevezes = data.megnevezes;
                let nettoAr = data.nettoAr;
                let afa = data.afa;

                if (search) {
                    const regex = new RegExp(`(${search})`, 'gi');
                    cikkszam = data.cikkszam.replace(regex, '<span class="searched">$1</span>');
                    megnevezes = data.megnevezes.replace(regex, '<span class="searched">$1</span>');
                    nettoAr = String(nettoAr).replace(regex, '<span class="searched">$1</span>');
                }

                tableRow.innerHTML = `
                    <td>${cikkszam}</td>
                    <td>${megnevezes}</td>
                    <td>${nettoAr} Ft</td>
                    <td>${afa}%</td>
                `;

                document.getElementById("itemsTable").appendChild(tableRow);
            }
        });
    }
</script>

</html>