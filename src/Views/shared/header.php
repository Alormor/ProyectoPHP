<div class="div-header">
    <div class="sup-header">
        <div class="envio">
            <ion-icon name="airplane-outline"></ion-icon>
            <span>Envíos gratis en compras superiores a 50€</span>
        </div>
        <div class="social-icons">
            <a href="https://www.instagram.com/" target="blanck"><ion-icon name="logo-instagram"></ion-icon></a>
            <a href="https://www.facebook.com/" target="blanck"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://www.tiktok.com/" target="blanck"><ion-icon name="logo-tiktok"></ion-icon></a>
        </div>
    </div>

    <div class="main-header">
        
    
        <div class="div-img-header">
            <img class="img-header" src="<?php echo $_ENV['BASE_URL']; ?>/images/logo-letras.png" alt="Imagen logo cubo de rubik">
        </div>
        <div class="navigation">
            <ul>
                <li class="list">
                    <a href="<?php echo $_ENV['BASE_URL']; ?>/">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="text">Inicio</span>
                    </a>
                </li>

           <li class="list" >
               <a href="<?php echo $_ENV['BASE_URL']; ?>/usuarios/userprofile">
                    <span class="icon">
                        <ion-icon name="person-outline"></ion-icon>
                    </span>
                    <span class="text">Perfil</span>
                </a>
            </li>

        <li class="list">
             <a href="<?php echo $_ENV['BASE_URL']; ?>/carrito">
                <span class="icon">
                    <ion-icon name="cart-outline"></ion-icon>
                </span>
                <span class="text">Carrito</span>
             </a>
        </li>
        <div class="indicator"></div>
        </ul>
        </div>
    </div>
</div>

<script src="<?php echo $_ENV['BASE_URL']; ?>/js/scriptHeader.js"></script>