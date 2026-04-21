// Esperamos a que el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
    const list = document.querySelectorAll('.list');
    const navigation = document.querySelector('.navigation');
    const indicator = document.querySelector('.indicator'); // Seleccionamos el círculo

    function activeLink() {
        navigation.classList.add('active-clicked');
        list.forEach((item) => item.classList.remove('active'));
        this.classList.add('active');

        // LEER EL COLOR: Obtenemos el color del atributo data-color del <li>
        const color = this.getAttribute('data-color');
        // APLICAR EL COLOR: Se lo pasamos al indicador como una variable CSS
        indicator.style.backgroundColor = color;
    }

    list.forEach((item) => item.addEventListener('click', activeLink));
});