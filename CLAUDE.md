# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Point of Sale (POS) system called "Aladdin" built with Laravel 12 and TailwindCSS. It's a multi-outlet retail management system with role-based access control for administrators, supervisors, and cashiers.

## Development Commands

### Laravel Commands
- `php artisan serve` - Start development server
- `php artisan migrate` - Run database migrations
- `php artisan migrate:fresh --seed` - Fresh migration with seeders
- `php artisan queue:listen --tries=1` - Start queue worker
- `php artisan pail --timeout=0` - View logs in real-time
- `composer dev` - Start all services (server, queue, logs, vite) using concurrently

### Frontend Commands
- `npm run dev` - Start Vite development server
- `npm run build` - Build for production

### Testing
- `php artisan test` - Run PHPUnit tests (basic Laravel tests available)

## Architecture

### Database Schema
The system uses Laravel Eloquent ORM with the following core entities:
- **Outlets** - Multiple store locations
- **Users** - Staff with roles (admin, supervisor, kasir/cashier)
- **Products** - Items with categories, barcodes, and inventory tracking
- **Orders/OrderItems** - Sales transactions with line items
- **Inventory/InventoryHistory** - Stock management with approval workflow
- **CashRegister/CashRegisterTransaction** - Cash flow tracking per shift
- **Shifts** - Work periods for cashiers
- **Members** - Customer loyalty system
- **Categories** - Product categorization

### Key Features
1. **Multi-outlet Management** - Each outlet maintains separate inventory
2. **Role-based Access Control** - Three user roles with different permissions
3. **Barcode System** - Product scanning with milon/barcode package
4. **Receipt Printing** - ESC/POS thermal printer support via mike42/escpos-php
5. **Stock Management** - Transfer between outlets, adjustment approvals
6. **Financial Reporting** - Daily/monthly sales, inventory reports
7. **Member System** - Customer tracking and history

### API Structure
- Authentication uses Laravel Sanctum
- RESTful API routes in `routes/api.php`
- Role middleware restricts access: `role:admin`, `role:admin,supervisor`, `role:kasir,admin,supervisor`
- Controllers follow standard CRUD patterns with additional business logic

### File Organization
- **Controllers**: Business logic in `app/Http/Controllers/`
- **Models**: Eloquent models in `app/Models/` with relationships
- **Views**: Blade templates in `resources/views/` (dashboard, POS interface)
- **Migrations**: Database schema in `database/migrations/`
- **Seeders**: Sample data in `database/seeders/`
- **Frontend**: TailwindCSS styling, minimal JavaScript in `resources/js/`

### Key Packages
- **darkaonline/l5-swagger**: API documentation
- **milon/barcode**: Barcode generation
- **mike42/escpos-php**: Thermal printer integration

### Configuration Notes
- Print templates are configurable per outlet
- File uploads stored in `public/uploads/` (products, logos, QR codes)
- Uses soft deletes for most entities
- Timestamp tracking on all models

## Coding Guidelines

### Language and Localization
- Always use Bahasa Indonesia for content generation and documentation