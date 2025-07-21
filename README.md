# NEPT GADGETS – Phone and Accessories Shop

A modern online shop for mobile phones, accessories, and electronics, built with PHP and MySQL.

## Features
- Product catalog with categories (Phones, Accessories)
- Product details with images, description, and price
- WhatsApp and Telegram direct inquiry buttons
- User registration/login (only logged-in users can message)
- Admin dashboard for managing products and users
- Responsive, modern UI (Bootstrap 5)
- Installer for easy setup

## Installation
1. **Clone or upload the project to your web server.**
2. **Create a MySQL database** (or use the installer to do this).
3. **Visit `/install/index.php` in your browser** to run the installer:
   - Enter your database credentials
   - Set up the initial admin account
   - The installer will create tables and write config
4. **Log in as admin** at `/admin/index.php` to manage products and users.
5. **Update business info** in `config/config.php` if needed.

## Default Admin Login
- Username: (set during install)
- Password: (set during install)

## Notes
- Only admins can add/remove products and users.
- Only logged-in users can send WhatsApp/Telegram inquiries.
- Make sure `assets/` is writable for image uploads.

## Contact
- Location: Entebbe
- Phone: +256752562531
