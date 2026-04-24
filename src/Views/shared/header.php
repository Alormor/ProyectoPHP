<div><img src="<?php echo $_ENV['BASE_URL']; ?>/images/letras-logo.png" alt="Logo"></div>
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
             <a href="#">
                <span class="icon">
                    <ion-icon name="cart-outline"></ion-icon>
                </span>
                <span class="text">Carrito</span>
             </a>
        </li>

        <li class="list">
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