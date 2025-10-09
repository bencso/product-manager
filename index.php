<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
</head>

<body>
    <h1>Bejelentkezés</h1>
    <form id="loginForm">
        <label for="username">Felhasználónév:</label>
        <input type="text" id="username" name="username" />
        <label for="username">Jelszó:</label>
        <input type="password" id="password" name="password" />
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