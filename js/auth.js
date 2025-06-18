document.addEventListener('DOMContentLoaded', function() {
    // Элементы модального окна
    const authModal = document.getElementById('authModal');
    const userProfileBtn = document.getElementById('userProfileBtn');
    const closeBtn = document.querySelector('.auth-close-btn');
    
    // Функция открытия модального окна
    function openAuthModal() {
        console.log('[DEBUG] Opening auth modal');
        authModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    
    // Функция закрытия модального окна
    function closeAuthModal() {
        console.log('[DEBUG] Closing auth modal');
        authModal.style.display = 'none';
        document.body.style.overflow = '';
    }
    
    // Обработчик кнопки профиля
    userProfileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        
        // Проверяем, авторизован ли пользователь
        checkAuthStatus().then(isAuthenticated => {
            if (isAuthenticated) {
                // Если авторизован - переходим на страницу профиля
                window.location.href = 'profile.html';
            } else {
                // Если не авторизован - показываем модальное окно
                openAuthModal();
            }
        });
        
        return false;
    }, true);
    
    // Обработчик кнопки закрытия
    closeBtn.addEventListener('click', closeAuthModal);
    
    // Закрытие при клике на оверлей
    authModal.addEventListener('click', function(e) {
        if (e.target === authModal || e.target.classList.contains('auth-modal-overlay')) {
            closeAuthModal();
        }
    });
    
    // Переключение между вкладками
    const tabs = document.querySelectorAll('.auth-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Удаляем активные классы
            tabs.forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.auth-tab-content').forEach(c => {
                c.classList.remove('active');
            });
            
            // Добавляем активные классы
            this.classList.add('active');
            document.getElementById(`${tabId}Form`).classList.add('active');
        });
    });
    
    // Обработчик формы входа
    document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const errorElement = document.getElementById('loginError');
        errorElement.textContent = '';
        
        const formData = {
            email: form.email.value,
            password: form.password.value
        };
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Успешный вход
                errorElement.textContent = '';
                closeAuthModal();
                
                // Сохраняем данные пользователя
                localStorage.setItem('user', JSON.stringify(data.user));
                
                // Перенаправляем на страницу профиля
                window.location.href = 'profile.html';
            } else {
                errorElement.textContent = data.message || 'Ошибка входа';
            }
        } catch (error) {
            console.error('[ERROR] Login error:', error);
            errorElement.textContent = 'Ошибка соединения с сервером';
        }
    });
    
    // Обработчик формы регистрации
    document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const form = e.target;
        const errorElement = document.getElementById('registerError');
        errorElement.textContent = '';
        
        const formData = {
            name: form.name.value,
            email: form.email.value,
            password: form.password.value,
            confirm_password: form.confirm_password.value
        };
        
        // Клиентская валидация
        if (formData.password !== formData.confirm_password) {
            errorElement.textContent = 'Пароли не совпадают';
            return;
        }
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Успешная регистрация
                errorElement.textContent = '';
                alert(data.message);
                
                // Переключаем на вкладку входа
                document.querySelector('[data-tab="login"]').click();
                
                // Очищаем форму
                form.reset();
            } else {
                errorElement.textContent = data.message || 'Ошибка регистрации';
            }
        } catch (error) {
            console.error('[ERROR] Register error:', error);
            errorElement.textContent = 'Ошибка соединения с сервером';
        }
    });
    
    // Функция проверки статуса авторизации
    async function checkAuthStatus() {
        try {
            const response = await fetch('php/check-auth.php');
            const data = await response.json();
            return data.authenticated;
        } catch (error) {
            console.error('[ERROR] Auth check failed:', error);
            return false;
        }
    }
});

// Функции для страницы профиля (profile.html)
function initProfilePage() {
    // Загружаем данные пользователя
    loadUserProfile();
    
    // Инициализируем вкладки
    initProfileTabs();
    
    // Инициализируем редактирование полей
    initEditableFields();
    
    // Инициализируем обработку платежных методов
    initPaymentMethods();
}

function loadUserProfile() {
    // Проверяем, есть ли данные пользователя
    const user = JSON.parse(localStorage.getItem('user')) || {};
    
    // Если данных нет, перенаправляем на главную
    if (!user.id) {
        window.location.href = 'index.html';
        return;
    }
    
    // Заполняем данные профиля
    document.getElementById('userName').textContent = user.name || 'Не указано';
    document.getElementById('userEmail').textContent = user.email || 'Не указано';
    document.getElementById('userAvatar').textContent = user.name ? user.name.charAt(0).toUpperCase() : 'П';
    
    // Загружаем дополнительные данные профиля из БД
    fetch(`php/get-profile.php?id=${user.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Заполняем поля профиля
                document.getElementById('accountName').textContent = data.profile.name || user.name || 'Не указано';
                document.getElementById('accountEmail').textContent = data.profile.email || user.email || 'Не указано';
                document.getElementById('accountPhone').textContent = data.profile.phone || 'Не указано';
                document.getElementById('accountAddress').textContent = data.profile.address || 'Не указано';
                document.getElementById('accountRegDate').textContent = data.profile.reg_date || new Date().toLocaleDateString();
            }
        })
        .catch(error => console.error('[ERROR] Profile load error:', error));
}

function initProfileTabs() {
    const tabs = document.querySelectorAll('.profile-tab');
    const panes = document.querySelectorAll('.tab-pane');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Удаляем активные классы
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            
            // Добавляем активные классы
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
}

function initEditableFields() {
    // Обработчик сохранения изменений профиля
    document.getElementById('saveAccountBtn').addEventListener('click', function() {
        const user = JSON.parse(localStorage.getItem('user'));
        if (!user || !user.id) return;
        
        const profileData = {
            id: user.id,
            name: document.getElementById('accountNameInput').value || document.getElementById('accountName').textContent,
            email: document.getElementById('accountEmailInput').value || document.getElementById('accountEmail').textContent,
            phone: document.getElementById('accountPhoneInput').value || document.getElementById('accountPhone').textContent,
            address: document.getElementById('accountAddressInput').value || document.getElementById('accountAddress').textContent
        };
        
        // Отправляем данные на сервер
        fetch('php/update-profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(profileData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем данные на странице
                document.getElementById('accountName').textContent = profileData.name;
                document.getElementById('accountEmail').textContent = profileData.email;
                document.getElementById('accountPhone').textContent = profileData.phone;
                document.getElementById('accountAddress').textContent = profileData.address;
                
                // Скрываем поля ввода
                toggleEditField('accountName', false);
                toggleEditField('accountEmail', false);
                toggleEditField('accountPhone', false);
                toggleEditField('accountAddress', false);
                
                // Скрываем кнопку сохранения
                document.getElementById('saveAccountBtn').style.display = 'none';
                
                alert('Изменения сохранены успешно!');
            } else {
                alert('Ошибка при сохранении: ' + (data.message || 'Неизвестная ошибка'));
            }
        })
        .catch(error => {
            console.error('[ERROR] Profile update error:', error);
            alert('Ошибка соединения с сервером');
        });
    });
}

function enableEdit(fieldId) {
    const valueElement = document.getElementById(fieldId);
    const inputElement = document.getElementById(`${fieldId}Input`);
    
    // Показываем поле ввода
    inputElement.value = valueElement.textContent;
    valueElement.style.display = 'none';
    inputElement.style.display = 'block';
    inputElement.focus();
    
    // Показываем кнопку сохранения
    document.getElementById('saveAccountBtn').style.display = 'inline-block';
}

function toggleEditField(fieldId, showInput) {
    const valueElement = document.getElementById(fieldId);
    const inputElement = document.getElementById(`${fieldId}Input`);
    
    if (showInput) {
        inputElement.value = valueElement.textContent;
        valueElement.style.display = 'none';
        inputElement.style.display = 'block';
    } else {
        valueElement.style.display = 'block';
        inputElement.style.display = 'none';
    }
}

function initPaymentMethods() {
    // Загрузка платежных методов из БД
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user || !user.id) return;
    
    fetch(`php/get-payment-methods.php?user_id=${user.id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.methods.length > 0) {
                const container = document.getElementById('paymentMethods');
                container.innerHTML = '';
                
                data.methods.forEach(method => {
                    const methodElement = document.createElement('div');
                    methodElement.className = 'payment-method';
                    methodElement.innerHTML = `
                        <div>•••• •••• •••• ${method.last_four}</div>
                        <div>${method.brand}</div>
                        <div class="payment-actions">
                            <button class="btn btn-primary" onclick="editPaymentMethod(this)">Изменить</button>
                            <button class="btn btn-danger" onclick="deletePaymentMethod(this, ${method.id})">Удалить</button>
                        </div>
                    `;
                    container.appendChild(methodElement);
                });
            }
        })
        .catch(error => console.error('[ERROR] Payment methods load error:', error));
}

function showAddCardForm() {
    document.getElementById('addCardForm').style.display = 'block';
}

function hideAddCardForm() {
    document.getElementById('addCardForm').style.display = 'none';
}

function savePaymentMethod() {
    const cardNumber = document.getElementById('cardNumber').value;
    const cardExpiry = document.getElementById('cardExpiry').value;
    const cardCvv = document.getElementById('cardCvv').value;
    const cardHolder = document.getElementById('cardHolder').value;
    
    // Валидация данных
    if (!cardNumber || !cardExpiry || !cardCvv || !cardHolder) {
        alert('Пожалуйста, заполните все поля');
        return;
    }
    
    const user = JSON.parse(localStorage.getItem('user'));
    if (!user || !user.id) return;
    
    const paymentData = {
        user_id: user.id,
        card_number: cardNumber,
        card_expiry: cardExpiry,
        card_cvv: cardCvv,
        card_holder: cardHolder
    };
    
    // Отправляем данные на сервер
    fetch('php/add-payment-method.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(paymentData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Карта успешно добавлена!');
            hideAddCardForm();
            initPaymentMethods(); // Перезагружаем список карт
        } else {
            alert('Ошибка: ' + (data.message || 'Не удалось добавить карту'));
        }
    })
    .catch(error => {
        console.error('[ERROR] Add payment method error:', error);
        alert('Ошибка соединения с сервером');
    });
}

function deletePaymentMethod(button, methodId) {
    if (!confirm('Вы уверены, что хотите удалить этот метод оплаты?')) return;
    
    fetch(`php/delete-payment-method.php?id=${methodId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.closest('.payment-method').remove();
            alert('Метод оплаты удален');
        } else {
            alert('Ошибка: ' + (data.message || 'Не удалось удалить метод оплаты'));
        }
    })
    .catch(error => {
        console.error('[ERROR] Delete payment method error:', error);
        alert('Ошибка соединения с сервером');
    });
}

function logout() {
    fetch('php/logout.php')
        .then(() => {
            localStorage.removeItem('user');
            window.location.href = 'index.html';
        })
        .catch(error => {
            console.error('[ERROR] Logout error:', error);
            alert('Ошибка при выходе из системы');
        });
}

// Инициализация страницы профиля
if (document.querySelector('.profile-container')) {
    initProfilePage();
}