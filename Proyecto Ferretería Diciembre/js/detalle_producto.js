document.getElementById('addToCartButton').addEventListener('click', function () {
    const form = document.getElementById('addToCartForm');
    const formData = new FormData(form);

    fetch('carrito.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            // Manejar errores HTTP
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json(); // Convertir la respuesta a JSON
    })
    .then(data => {
        console.log('Respuesta del servidor:', data); // Log para depuración
        if (data.success) {
            alert(data.success); // Mostrar mensaje de éxito
            updateCartCount(data.cart_count); // Actualizar el número del carrito
            window.location.reload(); // Recargar la página
        } else if (data.error) {
            alert(data.error); // Mostrar mensaje de error
        }
    })
    .catch(error => console.error('Error en la solicitud:', error));
});

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count; // Actualizar el número del carrito
    }
}
