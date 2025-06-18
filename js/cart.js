// cart.js - исправленная версия
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart system initialized');

    // Основная функция добавления в корзину
    window.addToCart = async function(album) {
        try {
            if (!album || !album.title) {
                throw new Error('Неверные данные альбома');
            }

            console.log("Отправка в корзину:", album);
            
            const response = await fetch('php/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    title: album.title,
                    artist: album.artist || '',
                    image: album.image || '',
                    price: album.price || 0
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log("Ответ сервера:", result);

            if (result.success) {
                alert('Успешно добавлено в корзину!');
                updateCartCount();
                return true;
            } else {
                throw new Error(result.message || 'Неизвестная ошибка сервера');
            }
        } catch (error) {
            console.error('Ошибка добавления:', error);
            alert('Ошибка: ' + error.message);
            return false;
        }
    };

    // Обновление счетчика корзины
    function updateCartCount() {
        if (!isUserLoggedIn()) {
            const counter = document.getElementById('cartCount');
            if (counter) counter.textContent = '0';
            return;
        }

        fetch(`php/get_cart_count.php?user_id=${JSON.parse(localStorage.getItem('user')).id}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const counter = document.getElementById('cartCount');
                if (counter) counter.textContent = data.count || '0';
            })
            .catch(error => {
                console.error('Ошибка обновления счетчика:', error);
            });
    }

    window.updateCartDisplay = async function() {
    if (window.location.pathname.includes('market.html')) {
        await loadCart();
    } else {
        updateCartCount();
    }
};

    // Проверка авторизации
    function isUserLoggedIn() {
        const user = localStorage.getItem('user');
        return user !== null && user !== undefined;
    }

    // Инициализация
    if (isUserLoggedIn()) {
        updateCartCount();
    }
});