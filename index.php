<?php
require "header.php";
?>
<title>Bejelentkezés</title>
<link rel="stylesheet" href="styles/index.styles.css">
</head>

<body>
    <div id="error">
        <span class="material-symbols-outlined">
            error
        </span>
        <span id="errorContent"></p>
    </div>
    <form id="loginForm" class="authForm">
        <h1>Bejelentkezés</h1>
        <div class="formInput">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" />
        </div>
        <div class="formInput">
            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" />
        </div>
        <div>
            <p>Ha még nincs fiókja <a href="/phpfeladat/registration">kattintson ide!</a></p>
        </div>
        <button type="submit">Bejelentkezés</button>
    </form>
</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        fetch("api/valid.php", {
                method: "POST",
                credentials: "include"
            })
            .then((res) => {
                return res.json()
            })
            .then((data) => {
                if (data.status == 200) window.location.href = "/phpfeladat/termekek";
            })
            .catch(err => {
                console.error("Validation error:", err);
            });
    });

    document.getElementById("loginForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch("./api/login.php", {
                method: "POST",
                body: formData
            }).then(res => {
                return res.json();
            })
            .then(
                data => {
                    const errorElement = document.getElementById("error");
                    const errorContentElement = document.getElementById("errorContent");

                    if (data.status === 200) {
                        window.location.href = '/phpfeladat/termekek';
                    } else {
                        document.querySelectorAll("input").forEach((input) => {
                            input.value = "";
                        });
                        errorContentElement.textContent = data.message;
                        errorElement.style.opacity = 1;
                        setTimeout(() => {
                            errorElement.style.opacity = 0;
                        }, 3000);
                    }
                }).catch(err => {
                console.error(err);
            })
    })
</script>

</html>