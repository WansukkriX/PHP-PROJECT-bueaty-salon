<style>

    .admin-navbar {
            background: #34495e;
            padding: 0.75rem 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            
        }
        .admin-navbar .navbar-brand {
            font-size: 1.5rem;
            transition: color 0.3s ease;
            
        }
        .admin-navbar .navbar-brand:hover {
            color: #6ab0f0;
        }
        .admin-navbar .nav-link {
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }
        .admin-navbar .nav-link:hover {
            color: #6ab0f0;
        }
        .admin-navbar .btn-danger {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
                body {
                    background: #f5f7fa;
                    font-family: 'Segoe UI', sans-serif;
                    color: #2c3e50;
                }

                .container {
                    max-width: 1400px;
                }

                h1 {
                    font-weight: 700;
                    color: #34495e;
                    text-align: center;
                    margin-bottom: 2rem;
                }

                .card {
                    border: none;
                    border-radius: 20px;
                    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    background: #fff;
                }

                .card-header {
                    background: #eef2f7;
                    color: #34495e;
                    font-weight: 600;
                    border-radius: 20px 20px 0 0;
                    padding: 1rem 1.5rem;
                }

                .btn-modern {
                    border-radius: 50px;
                    padding: 0.5rem 1.5rem;
                    font-weight: 500;
                    transition: all 0.3s ease;
                }

                .btn-primary {
                    background: #6ab0f0;
                    border: none;
                }

                .btn-primary:hover {
                    background: #4a90e2;
                }

                .btn-success {
                    background: #6edaa6;
                    border: none;
                }

                .btn-success:hover {
                    background: #4db88e;
                }

                .btn-warning {
                    background: #feca57;
                    border: none;
                    color: #333;
                }

                .btn-warning:hover {
                    background: #e6b04e;
                }

                .btn-danger {
                    background: #ff7675;
                    border: none;
                }

                .btn-danger:hover {
                    background: #e65b5a;
                }

                .modal-content {
                    border-radius: 20px;
                    background: #fff;
                }

                .table {
                    border-radius: 10px;
                    overflow: hidden;
                }

                .table thead th {
                    background: #eef2f7;
                    color: #34495e;
                }

                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 1.5rem;
                }

                canvas {
                    max-height: 300px;
                    border-radius: 10px;
                    background: #fff;
                    padding: 1rem;
                }
    </style>