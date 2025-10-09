// Termék tesztadatok
const datas = [{
    "cikkszam": "d12750689",
    "megnevezes": "ESR Aura Wallet Stand Bright White",
    "nettoAr": 6760,
    "afa": 27
},
{
    "cikkszam": "d5025253",
    "megnevezes": "RODE DeadKitten",
    "nettoAr": 11390,
    "afa": 27
},
{
    "cikkszam": "d13079115",
    "megnevezes": "iPhone 17 Pro Max 256 GB Kozmosznarancs",
    "nettoAr": 600000,
    "afa": 18
},
{
    "cikkszam": "d7774652",
    "megnevezes": "FIFINE BM63",
    "nettoAr": 15890,
    "afa": 27
},
{
    "cikkszam": "d7404138",
    "megnevezes": "XP-Pen grafikus kesztyű - L",
    "nettoAr": 5190,
    "afa": 15
},
{
    "cikkszam": "d5269024",
    "megnevezes": "Szarvasi SZV-624 Unipress bordó",
    "nettoAr": 32990,
    "afa": 10
},
{
    "cikkszam": "d6726929",
    "megnevezes": "Siguro Espresso Thermo pohár, 90 ml, 2 db",
    "nettoAr": 2990,
    "afa": 27
},
{
    "cikkszam": "d5833929",
    "megnevezes": "Lavazza Gusto Forte, szemes, 1000 g",
    "nettoAr": 6290,
    "afa": 18
}
]

const datasCopy = [...datas];

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
                eventListeners();
            }
        })
        .catch(err => {
            document.cookie = 'token=; Max-Age=0; path=/; domain=' + location.host;
            window.location.href = "/phpfeladat";
        });
});

function eventListeners() {
    document.getElementById("logoutBtn").addEventListener("click", () => {
        document.cookie = 'token=; Max-Age=0; path=/; domain=' + location.host;
        window.location.href = "/phpfeladat";
    });

    document.getElementById("searchInput").addEventListener("input", (e) => {
        loadTable(e.target.value);
    });

    document.getElementById("cikkszamOszlop").addEventListener("click", (e) => {
        switchSort("cikkszamOszlop");
        loadTable(document.getElementById("searchInput").value);
    });

    document.getElementById("megnevezesOszlop").addEventListener("click", (e) => {
        switchSort("megnevezesOszlop");
        loadTable(document.getElementById("searchInput").value);
    });

    document.getElementById("nettoarOszlop").addEventListener("click", (e) => {
        switchSort("nettoarOszlop");
        loadTable(document.getElementById("searchInput").value);
    });

    document.getElementById("afaOszlop").addEventListener("click", (e) => {
        switchSort("afaOszlop");
        loadTable(document.getElementById("searchInput").value);
    });
}

function switchSort(sortType) {
    const sortNumber = +document.getElementById(sortType).getAttribute("data-sorrend");
    if (sortNumber == 0) document.getElementById(sortType).setAttribute("data-sorrend", "1");
    else if (sortNumber == 1) document.getElementById(sortType).setAttribute("data-sorrend", "2");
    else document.getElementById(sortType).setAttribute("data-sorrend", "0");
}

function loadTable(search) {
    const rows = document.getElementById("itemsTable").getElementsByTagName("tr");
    while (rows.length > 2) {
        rows[2].remove();
    }

    let cikkszamSort = document.getElementById("cikkszamOszlop").getAttribute("data-sorrend");
    let megnevezesSort = document.getElementById("megnevezesOszlop").getAttribute("data-sorrend");
    let nettoArSort = document.getElementById("nettoarOszlop").getAttribute("data-sorrend");
    let afaSort = document.getElementById("afaOszlop").getAttribute("data-sorrend");

    if (cikkszamSort !== "0" || megnevezesSort !== "0" || nettoArSort !== "0") {
        datas.sort((a, b) => {
            if (cikkszamSort !== "0") {
                const cikkszamCompare = a.cikkszam.localeCompare(b.cikkszam);
                if (cikkszamCompare !== 0) {
                    return cikkszamSort === "1" ? cikkszamCompare : -cikkszamCompare;
                }
            }

            if (megnevezesSort !== "0") {
                const megnevezesCompare = a.megnevezes.localeCompare(b.megnevezes);
                if (megnevezesCompare !== 0) {
                    return megnevezesSort === "1" ? megnevezesCompare : -megnevezesCompare;
                }
            }

            if (nettoArSort !== "0") {
                const arResult = a.nettoAr - b.nettoAr;
                return nettoArSort === "1" ? arResult : -arResult;
            }

            if (afaSort !== "0") {
                const afaResult = a.afa - b.afa;
                return nettoArSort === "1" ? afaResult : -afaResult;
            }

            return 0;
        });
    } else {
        datas.length = 0;
        datas.push(...datasCopy);
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