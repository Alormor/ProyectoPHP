<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="/ProyectoPHP/public/css/styleHeader.css">
</head>
<body>
    <div><img src="/ProyectoPHP/public/images/letras-logo.png"></div>
    <div class="navigation">
        <ul>
            <li class="list">
                <a href="#">
                    <span class="icon">
                        <ion-icon name="home-outline"></ion-icon>
                    </span>
                    <span class="text">Inicio</span>
                </a>
            </li>

            <li class="list" data-color="#FFFF00">
                 <a href="#">
                    <span class="icon">
                        <ion-icon name="person-outline"></ion-icon>
                    </span>
                    <span class="text">Perfil</span>
                 </a>
            </li>

            <li class="list" data-color="#FF8000">
                 <a href="#">
                    <span class="icon">
                        <ion-icon name="cart-outline"></ion-icon>
                    </span>
                    <span class="text">Carrito</span>
                 </a>
            </li>

            <li class="list" data-color="#f44336">
                 <a href="#">
                    <span class="icon">
                        <ion-icon name="heart-outline"></ion-icon>
                    </span>
                    <span class="text">Favoritos</span>
                 </a>
            </li>
            <div class="indicator"></div>
        </ul>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <script src="/ProyectoPHP/public/js/scriptHeader.js"></script>
</body>
</html>