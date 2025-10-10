// Termék tesztadatok
let datas;
let datasCopy;

document.addEventListener("DOMContentLoaded", () => {
    fetch("../api/valid.php", {
        method: "POST",
        credentials: "include"
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.status !== 200) {
                window.location.href = "/phpfeladat";
            } else {
                eventListeners();
                fetchProducts("", "");
            }
        })
        .catch(err => {
            window.location.href = "/phpfeladat";
        });
});

function fetchProducts(sorts, search) {
    const params = new URLSearchParams();
    if (sorts.cikkszam) params.set("cikkszam", sorts.cikkszam);
    if (sorts.cikk_megnevezes) params.set("cikk_megnevezes", sorts.cikk_megnevezes);
    if (sorts.nettoar) params.set("nettoar", sorts.nettoar);
    if (sorts.afa) params.set("afa", sorts.afa);
    if (search && search.trim() !== "") params.set("search", search.trim());

    fetch("../api/product.php?" + params.toString(), {
        method: "GET",
        credentials: "include"
    })
        .then((res) => res.json())
        .then((returnData) => {
            if (returnData.status === 200 && Array.isArray(returnData.data)) {
                datas = returnData.data.map(item => ({
                    cikkszam: String(item.cikkszam),
                    megnevezes: String(item.cikk_megnevezes),
                    nettoAr: Number(item.nettoAr),
                    afa: Number(item.afa)
                }));
                datasCopy = [...datas];
                loadTable(search);
            } else {
                datas = [];
                datasCopy = [];
                loadTable(search);
            }
        })
        .catch(err => {
            console.log(err);
        });
}

function eventListeners() {
    document.getElementById("logoutBtn").addEventListener("click", () => {
        fetch("../api/logout.php", {
            method: "GET",
            credentials: "include"
        })
        window.location.href = "/phpfeladat";
    });

    document.getElementById("searchInput").addEventListener("input", (e) => {
        const search = e.target.value;
        if (search.trim() === "") {
            fetchProducts(sortLogic(), "");
        } else {
            fetchProducts(sortLogic(), search);
        }
    });

    document.getElementById("cikkszamOszlop").addEventListener("click", () => {
        switchSort("cikkszamOszlop");
        fetchProducts(sortLogic(), document.getElementById("searchInput").value);
    });

    document.getElementById("megnevezesOszlop").addEventListener("click", () => {
        switchSort("megnevezesOszlop");
        fetchProducts(sortLogic(), document.getElementById("searchInput").value);
    });

    document.getElementById("nettoarOszlop").addEventListener("click", () => {
        switchSort("nettoarOszlop");
        fetchProducts(sortLogic(), document.getElementById("searchInput").value);
    });

    document.getElementById("afaOszlop").addEventListener("click", () => {
        switchSort("afaOszlop");
        fetchProducts(sortLogic(), document.getElementById("searchInput").value);
    });
}

function switchSort(sortType) {
    const sortNumber = +document.getElementById(sortType).getAttribute("data-sorrend");
    if (sortNumber == 0) document.getElementById(sortType).setAttribute("data-sorrend", "1");
    else if (sortNumber == 1) document.getElementById(sortType).setAttribute("data-sorrend", "2");
    else document.getElementById(sortType).setAttribute("data-sorrend", "0");
}

function sortLogic() {
    let cikkszamSort = document.getElementById("cikkszamOszlop").getAttribute("data-sorrend");
    let megnevezesSort = document.getElementById("megnevezesOszlop").getAttribute("data-sorrend");
    let nettoArSort = document.getElementById("nettoarOszlop").getAttribute("data-sorrend");
    let afaSort = document.getElementById("afaOszlop").getAttribute("data-sorrend");

    return {
        cikkszam: cikkszamSort === "1" ? "ASC" : cikkszamSort === "2" ? "DESC" : "",
        cikk_megnevezes: megnevezesSort === "1" ? "ASC" : megnevezesSort === "2" ? "DESC" : "",
        nettoar: nettoArSort === "1" ? "ASC" : nettoArSort === "2" ? "DESC" : "",
        afa: afaSort === "1" ? "ASC" : afaSort === "2" ? "DESC" : "",
    }
}

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