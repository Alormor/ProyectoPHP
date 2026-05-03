<div class="pago-container">
    <h2>Resumen de tu Pedido</h2>
    
    <div class="pago-info">
        <p><strong>Dirección:</strong> <?= $direccion['direccion'] ?>, <?= $direccion['localidad'] ?></p>
        <p><strong>Total a pagar:</strong> <span><?= number_format($total, 2) ?>€</span></p>
    </div>

    <div id="paypal-button-container"></div>
    
    <div class="pago-footer">
        <p>Pago 100% seguro procesado por PayPal</p>
    </div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=ATRXNlNxziN1EJtc7vR1qD-wrIpXR25BMgY6HxIPBn3jvqUm81dIQKRMM2oa2hpQ7ZswJ-x17ec3arAF&currency=EUR"></script>

<script>
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
            return fetch('/pago/capturar/' + data.orderID, { method: 'POST' })
                .then(res => res.json())
                .then(details => {
                    if(details.status === 'COMPLETED') {
                        window.location.href = '/mis-pedidos';
                    }
                });
        }
    }).render('#paypal-button-container');
</script>