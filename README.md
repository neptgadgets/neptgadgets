# NEPT GADGETS – Phone and Accessories Shop

A modern online shop for mobile phones, accessories, and electronics, built with PHP and MySQL.

## Features
- Product catalog with categories (Phones, Accessories)
- Product details with images, description, and price
- WhatsApp and Telegram direct inquiry buttons
- User registration/login (only logged-in users can message)
- Admin dashboard for managing products, users, and categories
- Responsive, modern UI (Bootstrap 5)
- Installer for easy setup
- Password reset, profile management, CSRF protection, secure file uploads

## Installation
1. **Upload all files and folders to your PHP web server.**
   - The structure should look like:
     - `/public` (user-facing pages, set as web root if possible)
     - `/admin` (admin dashboard)
     - `/includes`, `/config`, `/assets`, `/install`
2. **Set permissions:**
   - Make sure the `/assets` directory is writable (for image uploads):
     - `chmod 777 assets`
   - Make sure `config/config.php` is writable (for installer to save DB settings):
     - `chmod 666 config/config.php`
3. **Create a MySQL database** (or let the installer do it for you).
4. **Run the installer:**
   - Visit `/install/index.php` in your browser.
   - Follow the steps to check requirements, enter DB credentials, and create the admin account.
   - The installer will create all tables, default categories, and write your config.
5. **After install:**
   - Log in as admin at `/admin/index.php` to manage products, users, and categories.
   - Update business info in `config/config.php` if needed.
   - Remove or restrict access to `/install` for security.

## Default Admin Login
- Username: (set during install)
- Password: (set during install)

## Notes
- Only admins can add/remove products, users, and categories.
- Only logged-in users can send WhatsApp/Telegram inquiries.
- Make sure `assets/` is writable for image uploads.
- For best security, remove write permissions from `config/config.php` after install.

## Contact
- Location: Entebbe
- Phone: +256752562531
