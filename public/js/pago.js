paypal.Buttons({
    createOrder: function(data, actions) {
        return fetch('/ProyectoPHP/pago/crear-orden', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(order => {
            return order.id;
        });
    },
    onApprove: function(data, actions) {
        return fetch('/ProyectoPHP/pago/capturar/' + data.orderID, { method: 'POST' })
            .then(res => res.json())
            .then(details => {
                if(details.status === 'COMPLETED') {
                    window.location.href = '/ProyectoPHP/mis-pedidos';
                }
            });
    }
}).render('#paypal-button-container');

var simularBtn = document.getElementById('simular-pago');
if (simularBtn) {
    simularBtn.addEventListener('click', function () {
        fetch('/ProyectoPHP/pago/simular', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'COMPLETED') {
                    window.location.href = '/ProyectoPHP/mis-pedidos';
                } else {
                    alert('Simulación fallida');
                }
            }).catch(err => {
                alert('Error simulando pago');
            });
    });
}
