<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Aladdin - Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .btn-green {
            background-color: #22c55e;
        }
        .btn-green:hover {
            background-color: #86efac;
        }
        body {
            font-family: 'Arial', sans-serif;
        }
        
        /* Notification styles */
        .notification {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            transform: translateX(150%);
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            z-index: 1000;
            max-width: 350px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .notification.show {
            transform: translateX(0);
        }
        
        .notification.success {
            background-color: rgba(255, 107, 0, 0.9); /* green with transparency */
            color: white;
        }
        
        .notification.error {
            background-color: rgba(239, 68, 68, 0.9); /* Red with transparency */
            color: white;
        }
        
        .notification-icon {
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        
        .notification-close {
            margin-left: 1rem;
            cursor: pointer;
            flex-shrink: 0;
            opacity: 0.7;
            transition: opacity 0.2s;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center justify-center p-4">
    <!-- Notification container -->
    <div id="notification-container"></div>

    <!-- Logo and Title above the card -->
    <div class="text-center mb-8">
        <div class="flex justify-center mb-4">
          <img src="/images/logo.png" alt="Aladdin Karpet Logo" class="w-24 h-24 object-contain sidebar-icon" />
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Aladdin Karpet</h1>
        <p class="text-gray-600 mt-2">Sistem Manajemen Bisnis Terintegrasi</p>
    </div>
        <!-- Login Card -->
        <div class="bg-white p-4 pt-4 pb-2 rounded-lg shadow-lg w-full max-w-md">
            <div class="mb-6">
                <h2 class="text-2xl font-semibold text-gray-700 text-center">Login</h2>
                <p class="text-gray-500 text-center mt-2">Masukkan kredensial Anda untuk mengakses sistem</p>
            </div>

            <form id="loginForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Masukkan email"
                            class="w-full px-4 py-3 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                            required
                        >
                        <i data-lucide="user" class="absolute left-3 top-3 text-gray-400 w-5 h-5"></i>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="Masukkan password"
                            class="w-full px-4 py-3 pl-10 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                            required
                        >
                        <i data-lucide="lock" class="absolute left-3 top-3 text-gray-400 w-5 h-5"></i>
                        <i data-lucide="eye" class="absolute right-3 top-3 text-gray-400 w-5 h-5 cursor-pointer" id="togglePassword"></i>
                    </div>
                </div>

                <div class="mb-6 flex items-center">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember"
                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                    >
                    <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                </div>

                <button 
                    type="submit" 
                    class="w-full btn-green text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300 hover:shadow-md"
                >
                    Login
                </button>
            </form>
        </div>
  <div class="mt-8 text-center text-sm text-gray-500">
          © Copyright <span class="font-medium"><img src="/images/LogoIt.png" alt="IT Solution Logo" class="inline w-24 h-6 object-contain" /></span>
</div>

        
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle between eye and eye-off icons
            const icon = this;
            if (icon.getAttribute('data-lucide') === 'eye') {
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        });

        // Notification system
        function showNotification(type, message) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            const iconName = type === 'success' ? 'check-circle' : 'alert-circle';
            
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <div class="notification-icon">
                    <i data-lucide="${iconName}" class="w-5 h-5"></i>
                </div>
                <div class="notification-message flex-1 text-sm font-medium">${message}</div>
                <div class="notification-close">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </div>
            `;
            
            container.appendChild(notification);
            lucide.createIcons();
            
            // Show notification
            setTimeout(() => {
                notification.classList.add('show');
            }, 10);
            
            // Auto-remove after 5 seconds
            const autoRemove = setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            }, 5000);
            
            // Manual close
            notification.querySelector('.notification-close').addEventListener('click', () => {
                clearTimeout(autoRemove);
                notification.classList.remove('show');
                setTimeout(() => {
                    container.removeChild(notification);
                }, 300);
            });
        }

        // Example notification functions
        function showError(message) {
            showNotification('error', message);
        }

        function showSuccess(message) {
            showNotification('success', message);
        }

        // Modify the login submission handler in your HTML
        document.getElementById('loginForm').addEventListener('submit', async function (e) {
            e.preventDefault(); // Wajib untuk mencegah reload

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const response = await fetch('/login', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await response.json();

                if (!response.ok) {
                    return showError(data.message || 'Login gagal.');
                }

                // Simpan token dan data user dasar
                localStorage.setItem('token', data.data.token);
                localStorage.setItem('role', data.data.user.role);
                localStorage.setItem('user_id', data.data.user.id);
                localStorage.setItem('name', data.data.user.name);

                const userRole = data.data.user.role;

                // Hanya simpan outlet_name dan outlet_id jika bukan admin
                if (userRole !== 'admin') {
                    localStorage.setItem('outlet_name', data.data.user.outlet.name);
                    localStorage.setItem('outlet_id', data.data.user.outlet.id);
                }

                // Jika ada data shift, simpan juga
                if (data.data.user.last_shift) {
                    localStorage.setItem('shift_id', data.data.user.last_shift.id);
                    localStorage.setItem('shift_data', JSON.stringify(data.data.user.last_shift));
                }

                // Arahkan ke halaman sesuai role
                if (userRole === 'kasir') {
                    showSuccess('Login berhasil! Mengarahkan ke POS...');
                    setTimeout(() => window.location.href = '/pos', 1500);
                } else {
                    showSuccess('Login berhasil! Mengarahkan ke dashboard...');
                    setTimeout(() => window.location.href = '/dashboard', 1500);
                }

            } catch (error) {
                console.error('Login error:', error);
                showError('Terjadi kesalahan pada server.');
            }
        });

        // Demo notifications (can be removed in production)
        document.addEventListener('DOMContentLoaded', function() {
        });
    </script>
</body>
</html>