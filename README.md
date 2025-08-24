# 🚀 Laravel Starter Kit (Motkaml)

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-^8.2-blue?style=flat&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-orange?style=flat&logo=mysql)](https://www.mysql.com/)
[![NPM](https://img.shields.io/badge/NPM-^10-green?style=flat&logo=npm)](https://www.npmjs.com/)
[![License](https://img.shields.io/badge/License-MIT-lightgrey?style=flat)](LICENSE)

---

## 📖 Introduction
This project is a **Laravel Starter Kit** developed by **Motkaml Team**.  
It is designed to help developers kickstart any new Laravel project with all the **core features, CRUD generators, authentication, dashboard, and API-ready setup** already implemented.  

With this starter kit, you no longer need to start from scratch — everything is ready to build upon.

---

## ✨ Features
- 🔑 **Authentication System**  
  - User Registration / Login.  
  - Password Reset.  
  - API Authentication (Sanctum / Passport).  

- 📊 **Admin Dashboard**  
  - CRUD templates with Vuexy Admin Template.  
  - Ready-to-use blank pages for new modules.  

- 📦 **Pre-installed Packages**  
  - Notifications.  
  - Chat system.  
  - File upload, delete, and compression (images, videos, documents).  

- ⚙️ **Helpers & Traits**  
  - `helpers.php` with commonly used functions.  
  - `FileUploadTrait.php` for handling all file operations.  

- 🛠 **Models & Relationships**  
  - Predefined models with common relationships.  

- 🌱 **Seeders Ready**  
  - Initial data to get the project running instantly.  

- 🧩 **Pre-built CRUD Modules**  
  - Users Management.  
  - Contact Us.  
  - Application Settings.  
  - Pages.  

---

## 📂 Project Structure
```
├── app
│   ├── Http
│   │   ├── Controllers (Auth, Settings, Pages, Contact, ...)
│   ├── Models
│   ├── Traits (FileUploadTrait.php)
│
├── config
├── database
│   ├── migrations
│   ├── seeders
│
├── public
│   ├── uploads
│
├── resources
│   ├── views (Dashboard + CRUD templates)
│
├── routes
│   ├── web.php
│   ├── api.php
│
├── helpers.php
```

---

## 🚀 Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/motkaml/laravel-starter-kit.git
   cd laravel-starter-kit
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install && npm run dev
   ```

3. Copy environment file and generate app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Create database and run migrations with seeders:
   ```bash
   php artisan migrate --seed
   ```

5. Start the development server:
   ```bash
   php artisan serve
   ```

---

## 🔑 Default Credentials
- **Email:** `admin@motkaml.com`  
- **Password:** `password`  

---

## 📡 API Ready
This starter kit is fully **API-ready** with authentication support using:  
- [Laravel Sanctum](https://laravel.com/docs/sanctum)  
- [Laravel Passport](https://laravel.com/docs/passport)  

---

## 🤝 Contributing
We welcome contributions!  

1. Fork the repository.  
2. Create a new feature branch:  
   ```bash
   git checkout -b feature/my-feature
   ```
3. Commit your changes:  
   ```bash
   git commit -m "Added my feature"
   ```
4. Push to your branch:  
   ```bash
   git push origin feature/my-feature
   ```
5. Open a Pull Request.  

---

## 📜 License
This project is licensed under the [MIT License](LICENSE).  
You are free to use it for personal and commercial projects.

---

## 👨‍💻 Developed by
- **Motkaml Team** 💙
