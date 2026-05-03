<?php
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $basePath = parse_url($_ENV['BASE_URL'] ?? '', PHP_URL_PATH) ?: '';
    if ($basePath !== '' && str_starts_with($uri, $basePath)) {
        $uri = substr($uri, strlen($basePath));
    }
    $uri = '/' . trim($uri, '/');
    if ($uri === '/index.php') {
        $uri = '/';
    }
?>
<header class="div-header">
    <!-- Línea Superior de Envío y Redes Sociales -->
    <div class="sup-header">
        <div class="envio">
            <ion-icon name="airplane-outline"></ion-icon>
            <span>Envíos gratis en compras superiores a 50€</span>
        </div>
        <div class="social-icons">
            <a href="https://www.instagram.com" target="_blank"><ion-icon name="logo-instagram"></ion-icon></a>
            <a href="https://www.facebook.com" target="_blank"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://www.tiktok.com" target="_blank"><ion-icon name="logo-tiktok"></ion-icon></a>
        </div>
    </div>

    <!-- Header Principal -->
    <div class="main-header">
        <a href="<?php echo $_ENV['BASE_URL']; ?>/">
            <img class="img-header" src="<?php echo $_ENV['BASE_URL']; ?>/images/logo-letras.png" alt="Logo">
        </a>
        <div class="navigation">
            <ul>
                <li class="list <?php echo $uri === '/' ? 'active' : ''; ?>">
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/">
                        <span class="icon"><ion-icon name="home-outline"></ion-icon></span>
                        <span class="text">Inicio</span>
                    </a>
                </li>

                <li class="list <?php echo str_starts_with($uri, '/usuarios/userprofile') ? 'active' : ''; ?>">
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/usuarios/userprofile">
                        <span class="icon"><ion-icon name="person-outline"></ion-icon></span>
                        <span class="text">Perfil</span>
                    </a>
                </li>

                <li class="list <?php echo str_starts_with($uri, '/carrito') ? 'active' : ''; ?>">
                    <a href="<?= $_ENV['BASE_URL'] ?>/carrito">
                        <span class="icon"><ion-icon name="cart-outline"></ion-icon></span>
                        <span class="text">Carrito</span>
                    </a>
                </li>

                <li class="list <?php echo str_starts_with($uri, '/mis-pedidos') ? 'active' : ''; ?>">
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/mis-pedidos">
                        <span class="icon"><ion-icon name="bag-handle-outline"></ion-icon></span>
                        <span class="text">Pedidos</span>
                    </a>
                </li>
                <div class="indicator"></div>
            </ul>
        </div>
    </div>
</header>