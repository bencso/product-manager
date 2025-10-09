<?php
require "header.php";
?>
<title>Bejelentkezés</title>
<link rel="stylesheet" href="styles/index.styles.css">
</head>

<body>
    <form id="loginForm">
        <h1>Bejelentkezés</h1>
        <div class="formInput">
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" />
        </div>
        <div class="formInput">
            <label for="username">Jelszó:</label>
            <input type="password" id="password" name="password" />
        </div>
        <span id="error"></span>
        <button type="submit">Bejelentkezés</button>
    </form>
</body>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        if (document.cookie.includes("token=")) {
            fetch("api/valid.php", {
                    method: "POST",
                    credentials: "include"
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.status == 200) {
                        window.location.href = "/phpfeladat/termekek";
                    } else {
                        document.cookie = 'token=; Max-Age=0; path=/; domain=' + location.host;
                    }
                })
                .catch(err => {
                    console.error("Validation error:", err);
                });
        }
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

                    console.log(data);
                    if (data.status === 200) {
                        window.location.href = '/phpfeladat/termekek';
                    } else {
                        errorElement.textContent = data.message;
                        errorElement.style.color = "red";
                    }
                }).catch(err => {
                console.error(err);
            })
    })
</script>

</html>