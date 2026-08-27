<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Soma POS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            overflow-x: hidden;
        }

        /* Side Menu Styles */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: #06162f;
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
        }

        .sidebar.closed {
            left: -280px;
        }

        .sidebar-header {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-header h2 {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #c7d2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-header p {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 4px;
        }

        .nav-menu {
            padding: 12px 0 20px 0;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .nav-menu::-webkit-scrollbar {
            width: 4px;
        }

        .nav-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        .nav-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
        }

        .nav-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .nav-item {
            margin: 2px 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            gap: 12px;
            font-weight: 500;
            font-size: 14px;
            position: relative;
        }

        .nav-link i {
            width: 22px;
            font-size: 16px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(4px);
        }

        .nav-link.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .nav-link .badge {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: auto;
        }

        .nav-link .badge-blue {
            background: #2563eb;
            color: white;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: auto;
        }

        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.08);
            margin: 8px 20px;
        }

        /* Nav Dropdown Styles */
        .nav-dropdown {
            display: none;
            padding-left: 38px;
            margin-top: 2px;
            margin-bottom: 8px;
        }

        .nav-dropdown.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .nav-link.has-dropdown .chevron {
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 12px;
        }

        .nav-link.has-dropdown.open .chevron {
            transform: rotate(180deg);
        }

        .nav-dropdown-item {
            display: block;
            padding: 8px 12px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
            font-size: 13px;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-dropdown-item:before {
            content: '';
            position: absolute;
            left: -12px;
            top: 50%;
            width: 6px;
            height: 1px;
            background: rgba(255,255,255,0.2);
        }

        .nav-dropdown-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
            transform: translateX(4px);
        }

        .nav-dropdown-item.active {
            color: #60a5fa;
            font-weight: 600;
        }
        
        .outlet-switcher {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }
        .outlet-switcher:hover {
            background: #e2e8f0;
        }
        .outlet-switcher select {
            background: transparent;
            border: none;
            outline: none;
            font-weight: 600;
            color: #0f172a;
            cursor: pointer;
            padding-right: 8px;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 14px;
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            margin: 2px 12px;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .logout-btn i {
            width: 22px;
            font-size: 16px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        /* Top Bar */
        .top-bar {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            position: sticky;
            top: 0;
            z-index: 999;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 64px;
            flex-shrink: 0;
        }

        .menu-toggle {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #2563eb;
            display: none;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .menu-toggle:hover {
            background: #f3f4f6;
        }

        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .top-bar-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .top-bar-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        /* ========== USER DROPDOWN - STYLED ========== */
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 4px 8px 4px 4px;
            border-radius: 50px;
            transition: all 0.3s ease;
            position: relative;
            background: #f8fafc;
            border: 1.5px solid transparent;
        }

        .user-info:hover {
            background: #f1f5f9;
            border-color: #e2e8f0;
        }

        .user-info .user-avatar {
            width: 36px;
            height: 36px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 15px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        .user-info .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            max-width: 100px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-info .chevron-icon {
            color: #94a3b8;
            font-size: 11px;
            transition: transform 0.3s ease;
            margin-left: 2px;
        }

        .user-info .chevron-icon.rotated {
            transform: rotate(180deg);
        }

        /* User Dropdown Menu */
        .user-dropdown {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            min-width: 220px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(0, 0, 0, 0.04);
            display: none;
            flex-direction: column;
            z-index: 1000;
            overflow: hidden;
            animation: dropdownFadeIn 0.25s ease-out;
            padding: 8px;
        }

        .user-dropdown.show {
            display: flex;
        }

        @keyframes dropdownFadeIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .dropdown-header {
            padding: 12px 14px 14px 14px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 4px;
        }

        .dropdown-header-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .dropdown-header-email {
            font-size: 12px;
            color: #64748b;
            margin: 4px 0 0 0;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #334155;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            border-radius: 10px;
        }

        .dropdown-item i {
            width: 18px;
            font-size: 15px;
            color: #94a3b8;
            text-align: center;
            transition: color 0.2s ease;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .dropdown-item:hover i {
            color: #2563eb;
        }

        .dropdown-item.danger {
            color: #dc2626;
        }

        .dropdown-item.danger i {
            color: #f87171;
        }

        .dropdown-item.danger:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        .dropdown-item.danger:hover i {
            color: #dc2626;
        }

        .dropdown-divider {
            height: 1px;
            background: #f1f5f9;
            margin: 4px 8px;
        }

        /* Page Content */
        .page-content {
            padding: 0;
            flex: 1;
            background: #f8fafc;
        }

        /* Shared page theme utilities */
        .soma-page {
            min-height: 100%;
            padding: 24px;
        }

        .soma-page-inner {
            max-width: 1536px;
            margin: 0 auto;
        }

        .soma-card {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
        }

        .soma-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.75rem;
            background: #1d4ed8;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #fff;
            transition: background 0.2s;
        }

        .soma-btn-primary:hover {
            background: #1e40af;
        }

        .soma-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            transition: border-color 0.2s, background 0.2s;
        }

        .soma-btn-secondary:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .soma-input {
            width: 100%;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
            color: #0f172a;
            background: #fff;
        }

        .soma-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .soma-table thead {
            background: #f8fafc;
        }

        .soma-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
        }

        .soma-table td {
            padding: 1rem;
            font-size: 0.875rem;
            color: #334155;
        }

        .soma-table tbody tr {
            border-top: 1px solid #f1f5f9;
        }

        .soma-table tbody tr:hover {
            background: #f8fafc;
        }

        @media (max-width: 640px) {
            .page-content {
                padding: 16px;
            }
        }

        /* Main Footer - Single Line Dark Grey */
        .main-footer {
            background: #1f2937;
            padding: 12px 24px;
            flex-shrink: 0;
            margin-top: auto;
            border-top: 1px solid #374151;
        }

        .footer-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px 24px;
            max-width: 100%;
            margin: 0 auto;
        }

        .footer-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #d1d5db;
            font-size: 13px;
        }

        .footer-item i {
            color: #6366f1;
            font-size: 14px;
            width: 16px;
            text-align: center;
        }

        .footer-item .label {
            color: #9ca3af;
            font-weight: 400;
        }

        .footer-item .value {
            color: #f3f4f6;
            font-weight: 500;
        }

        .footer-item .value.green {
            color: #34d399;
            font-weight: 600;
        }

        .footer-divider {
            color: #374151;
            font-size: 16px;
            font-weight: 300;
        }

        @media (max-width: 768px) {
            .footer-content {
                gap: 6px 12px;
            }

            .footer-item {
                font-size: 11px;
            }

            .footer-divider {
                display: none;
            }

            .user-info .user-name {
                max-width: 60px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .footer-content {
                justify-content: center;
                gap: 4px 10px;
            }

            .footer-item {
                font-size: 10px;
            }

            .footer-item i {
                font-size: 11px;
            }

            .user-info .user-name {
                display: none;
            }
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }

        .mobile-overlay.active {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                left: -280px;
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .menu-toggle {
                display: block;
            }

            .top-bar-info {
                gap: 6px;
            }

            .status-indicator span {
                display: none;
            }

            .top-bar-title {
                font-size: 15px;
            }
        }

        /* Notification styles */
        .notification-slide {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Mobile Overlay -->
    <div id="mobileOverlay" class="mobile-overlay" onclick="closeMobileMenu()"></div>

    <!-- Sidebar Menu -->
    <div id="sidebar" class="sidebar">
        <div class="sidebar-header">
            <div class="flex items-center justify-center gap-3 mb-1">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-store text-white text-lg"></i>
                </div>
                <div class="text-left">
                    <h2 class="text-xl font-extrabold tracking-wide text-white">SOMA POS</h2>
                    <p class="text-xs text-slate-400">Retail terminal</p>
                </div>
            </div>
        </div>

        <div class="nav-menu">
            <!-- DASHBOARD -->
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <!-- REGISTER -->
            <div class="nav-item">
                <a href="{{ route('daily-register.index') }}" class="nav-link {{ request()->routeIs('daily-register.*') ? 'active' : '' }}">
                    <i class="fas fa-cash-register"></i>
                    <span>Register</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                    <i class="fas fa-cart-shopping"></i>
                    <span>New Sale</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- PRODUCTS -->
            <div class="nav-item">
                <a href="#" class="nav-link has-dropdown" onclick="toggleNavDropdown(event, 'productsDropdown')">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-dropdown" id="productsDropdown">
                    <a href="{{ route('products.index') }}" class="nav-dropdown-item">List Products</a>
                    <a href="{{ route('products.create') }}" class="nav-dropdown-item">Add Product</a>
                    <a href="{{ route('products.import-assigned') }}" class="nav-dropdown-item">Import Assigned Products</a>
                    <a href="{{ route('products.stock-breaking') }}" class="nav-dropdown-item">Stock Breaking</a>
                    <a href="{{ route('products.price-groups') }}" class="nav-dropdown-item">Selling Price Group</a>
                    <a href="{{ route('products.units') }}" class="nav-dropdown-item">Units</a>
                    <a href="{{ route('products.brands') }}" class="nav-dropdown-item">Brands</a>
                    <a href="{{ route('warranties.index') }}" class="nav-dropdown-item">Warranty</a>
                </div>
            </div>

            <!-- PURCHASES -->
            <div class="nav-item">
                <a href="#" class="nav-link has-dropdown" onclick="toggleNavDropdown(event, 'purchasesDropdown')">
                    <i class="fas fa-truck-ramp-box"></i>
                    <span>Purchases</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-dropdown" id="purchasesDropdown">
                    <a href="{{ route('purchases.index') }}" class="nav-dropdown-item">List Purchases</a>
                    <a href="{{ route('purchases.create') }}" class="nav-dropdown-item">Add Purchase</a>
                    <a href="{{ route('purchases.returns') }}" class="nav-dropdown-item">List Purchase Return</a>
                </div>
            </div>

            <!-- SALES -->
            <div class="nav-item">
                <a href="#" class="nav-link has-dropdown" onclick="toggleNavDropdown(event, 'salesDropdown')">
                    <i class="fas fa-cart-shopping"></i>
                    <span>Sales</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-dropdown" id="salesDropdown">
                    <a href="{{ route('reports.sales') }}" class="nav-dropdown-item">All Sales</a>
                    <a href="{{ route('sales.pos-list') }}" class="nav-dropdown-item">List POS</a>
                    <a href="{{ route('pos.index') }}" class="nav-dropdown-item">POS</a>
                    <a href="{{ route('sales.mpesa-transactions') }}" class="nav-dropdown-item">M-Pesa Transactions</a>
                    <a href="{{ route('quotations.index') }}" class="nav-dropdown-item">List Quotation</a>
                    <a href="{{ route('quotations.create') }}" class="nav-dropdown-item">Add Quotation</a>
                    <a href="{{ route('sales.discounts') }}" class="nav-dropdown-item">Discounts</a>
                    <a href="{{ route('sales.import') }}" class="nav-dropdown-item">Import Sale</a>
                </div>
            </div>

            <div class="nav-divider"></div>

            <!-- RETURNS -->
            <div class="nav-item">
                <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                    <i class="fas fa-undo-alt"></i>
                    <span>Returns</span>
                </a>
            </div>

            <!-- CREDITS -->
            <div class="nav-item">
                <a href="{{ route('credits.index') }}" class="nav-link {{ request()->routeIs('credits.*') ? 'active' : '' }}">
                    <i class="fas fa-credit-card"></i>
                    <span>Customer Credits</span>
                </a>
            </div>

            <div class="nav-divider"></div>

            <!-- STOCK TRANSFER -->
            <div class="nav-item">
                <a href="{{ route('stock-transfers.index') }}" class="nav-link">
                    <i class="fas fa-exchange-alt"></i>
                    <span>Stock Transfer</span>
                </a>
            </div>

            <!-- STOCK ADJUSTMENT -->
            <div class="nav-item">
                <a href="#" class="nav-link has-dropdown" onclick="toggleNavDropdown(event, 'stockAdjustmentDropdown')">
                    <i class="fas fa-sliders-h"></i>
                    <span>Stock Adjustment</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-dropdown" id="stockAdjustmentDropdown">
                    <a href="{{ route('stock-adjustments.index') }}" class="nav-dropdown-item">List Stock Adjustment</a>
                    <a href="{{ route('stock-adjustments.create') }}" class="nav-dropdown-item">Add Stock Adjustment</a>
                </div>
            </div>

            <!-- EXPENSES -->
            <div class="nav-item">
                <a href="#" class="nav-link has-dropdown" onclick="toggleNavDropdown(event, 'expensesDropdown')">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Expenses</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-dropdown" id="expensesDropdown">
                    <a href="{{ route('expenses.index') }}" class="nav-dropdown-item">List Expense</a>
                    <a href="{{ route('expenses.create') }}" class="nav-dropdown-item">Add Expense</a>
                    <a href="{{ route('expenses.categories') }}" class="nav-dropdown-item">Expense Category</a>
                </div>
            </div>

            <!-- REPORTS -->
            <div class="nav-item">
                <a href="#" class="nav-link has-dropdown" onclick="toggleNavDropdown(event, 'reportsDropdown')">
                    <i class="fas fa-chart-pie"></i>
                    <span>Reports</span>
                    <i class="fas fa-chevron-down chevron"></i>
                </a>
                <div class="nav-dropdown" id="reportsDropdown">
                    <a href="{{ route('reports.profit-loss') }}" class="nav-dropdown-item">Profit Loss</a>
                    <a href="{{ route('reports.product-sell') }}" class="nav-dropdown-item">Product Sell Report</a>
                    <a href="{{ route('reports.sell-payment') }}" class="nav-dropdown-item">Sell Payment Report</a>
                    <a href="{{ route('reports.purchase-sale') }}" class="nav-dropdown-item">Purchase & Sale Report</a>
                    <a href="{{ route('reports.tax') }}" class="nav-dropdown-item">Tax Report</a>
                   <a href="{{ route('reports.inventory') }}" class="nav-dropdown-item">Inventory Report</a>
                    <a href="{{ route('reports.employee-performance') }}" class="nav-dropdown-item">Employee Performance</a>
                </div>
            </div>

            <!-- ========== ADMIN ONLY SECTION ========== -->
            @if (auth()->check() && auth()->user()->isAdmin())
                <div class="nav-divider"></div>

                <div class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Admin Dashboard</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('users.index') }}"
                        class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="fas fa-users-cog"></i>
                        <span>User Management</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.outlets.index') }}"
                        class="nav-link {{ request()->routeIs('admin.outlets.*') ? 'active' : '' }}">
                        <i class="fas fa-store"></i>
                        <span>Branch Management</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.access-control') }}"
                        class="nav-link {{ request()->routeIs('admin.access-control') ? 'active' : '' }}">
                        <i class="fas fa-lock"></i>
                        <span>Access Control</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.activity-log') }}"
                        class="nav-link {{ request()->routeIs('admin.activity-log') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span>Activity Log</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.system-logs') }}"
                        class="nav-link {{ request()->routeIs('admin.system-logs') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>
                        <span>System Logs</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.settings') }}"
                        class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cogs"></i>
                        <span>System Settings</span>
                    </a>
                </div>
            @endif

        </div>
    </div>

    <!-- Main Content -->
    <div id="mainContent" class="main-content">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-left">
                <button class="menu-toggle" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="top-bar-title">@yield('header_title', 'Soma POS')</h1>
            </div>

            <div class="top-bar-info">
                <div class="status-indicator">
                    <div class="status-dot"></div>
                    <span>Online</span>
                </div>

                @if (auth()->user()->isAdmin())
                <div class="outlet-switcher">
                    <i class="fas fa-store"></i>
                    <form action="{{ route('admin.outlet.switch') }}" method="POST" style="margin: 0;" id="outletSwitcherForm">
                        @csrf
                        <select name="outlet_id" onchange="document.getElementById('outletSwitcherForm').submit()" style="background: transparent; border: none; font-size: 14px; font-weight: 500; color: var(--text-color); outline: none; cursor: pointer;">
                            @foreach(\App\Models\Outlet::where('is_active', true)->get() as $outlet)
                                <option value="{{ $outlet->id }}" {{ session('active_outlet_id', 1) == $outlet->id ? 'selected' : '' }}>
                                    {{ $outlet->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
                @else
                <div class="status-indicator">
                    <i class="fas fa-store"></i>
                    <span>{{ auth()->user()->outlet->name ?? 'No Outlet Assigned' }}</span>
                </div>
                @endif

                <div class="status-indicator">
                    <i class="fas fa-desktop"></i>
                    <span>TERM-01</span>
                </div>

                <!-- ========== STYLED USER DROPDOWN ========== -->
                <div class="user-info" onclick="toggleUserDropdown(event)">
                    <div class="user-avatar">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down chevron-icon" id="dropdownChevron"></i>

                    <!-- Dropdown Menu -->
                    <div id="userDropdown" class="user-dropdown">
                        <div class="dropdown-header">
                            <p class="dropdown-header-name">{{ Auth::user()->name }}</p>
                            <p class="dropdown-header-email">{{ Auth::user()->email ?? 'Administrator' }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>

                        <a href="#" class="dropdown-item"
                            onclick="event.preventDefault(); showNotification('Settings coming soon!', 'info');">
                            <i class="fas fa-cog"></i> Settings
                        </a>

                        <div class="dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}"
                            style="margin: 0; padding: 0; width: 100%;">
                            @csrf
                            <button type="submit" class="dropdown-item danger">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

        <!-- Main Footer - Single Line Dark Grey -->
        <footer class="main-footer">
            <div class="footer-content">
                <div class="footer-item">
                    <i class="fas fa-clock"></i>
                    <span class="label">Shift:</span>
                    <span class="value green">Open</span>
                </div>

                <span class="footer-divider">|</span>

                <div class="footer-item">
                    <i class="fas fa-hashtag"></i>
                    <span class="label">ID:</span>
                    <span class="value">SH-0008</span>
                </div>

                <span class="footer-divider">|</span>

                <div class="footer-item">
                    <i class="fas fa-money-bill-wave"></i>
                    <span class="label">Opening:</span>
                    <span class="value">KES 5,000.00</span>
                </div>

                <span class="footer-divider">|</span>

                <div class="footer-item">
                    <i class="fas fa-cash-register"></i>
                    <span class="label">Cash in Hand:</span>
                    <span class="value">KES 6,250.00</span>
                </div>

                <span class="footer-divider">|</span>

                <div class="footer-item">
                    <i class="fas fa-code-branch"></i>
                    <span class="label">v</span>
                    <span class="value">1.0.0</span>
                </div>
            </div>
        </footer>
    </div>

    <!-- Notification Container -->
    <div id="notification-container" class="fixed top-20 right-4 z-50 space-y-2"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Mobile menu functions
        function toggleMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');

            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');

            if (sidebar.classList.contains('mobile-open')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }

        function closeMobileMenu() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobileOverlay');

            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function toggleUserDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('dropdownChevron');

            dropdown.classList.toggle('show');
            chevron.classList.toggle('rotated');
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const chevron = document.getElementById('dropdownChevron');

            if (dropdown && dropdown.classList.contains('show') && !event.target.closest('.user-info')) {
                dropdown.classList.remove('show');
                chevron.classList.remove('rotated');
            }
        });

        // Close menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });

        function toggleNavDropdown(event, id) {
            event.preventDefault();
            const link = event.currentTarget;
            const dropdown = document.getElementById(id);
            
            link.classList.toggle('open');
            dropdown.classList.toggle('show');
        }

        // Global notification function
        window.showNotification = function(message, type = 'success') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };

            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };

            const notification = $(`
                    <div class="notification-slide ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 min-w-[280px]">
                        <i class="fas ${icons[type]}"></i>
                        <span>${message}</span>
                        <button class="ml-auto hover:opacity-75" onclick="$(this).parent().fadeOut(() => $(this).parent().remove())">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);

            $('#notification-container').append(notification);

            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 5000);
        };

        window.playSound = function(type = 'beep') {
            try {
                if (type === 'success') {
                    const audioContext = new(window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.value = 880;
                    gainNode.gain.value = 0.1;

                    oscillator.start();
                    setTimeout(() => {
                        oscillator.stop();
                    }, 200);
                }
            } catch (e) {
                console.log('Sound not supported');
            }
        };
    </script>

    @stack('scripts')
</body>

</html>
