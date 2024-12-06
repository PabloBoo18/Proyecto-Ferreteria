document.addEventListener('DOMContentLoaded', () => {
    // Función para agregar producto al carrito
    function addToCart(idProducto, cantidad) {
        fetch('carrito.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=add_to_cart&id_producto=${idProducto}&cantidad=${cantidad}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.success);
                setTimeout(() => location.reload(), 500); // Recargar la página después de agregar
            } else if (data.error) {
                alert(data.error);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Eliminar producto del carrito
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', () => {
            const idProducto = button.dataset.productId;

            fetch('carrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=remove_from_cart&id_producto=${idProducto}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    setTimeout(() => location.reload(), 500); // Recargar la página después de eliminar
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // Actualizar cantidad de un producto
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', () => {
            const idProducto = input.dataset.productId;
            const cantidad = input.value;

            fetch('carrito.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=update_quantity&id_producto=${idProducto}&cantidad=${cantidad}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.success);
                    setTimeout(() => location.reload(), 500); // Recargar la página después de actualizar la cantidad
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
