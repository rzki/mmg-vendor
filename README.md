# MMG Vendor

MMG Vendor is a Laravel-based web application designed to streamline vendor management for businesses, created specifically for PT. Medquest Mitra Global, one of the biggest healthcare device distributor in Indonesia. It provides tools for registering, tracking, and managing vendors, making it easier for organizations to maintain up-to-date records and improve their procurement processes.

## Who Is This For?

This project is ideal for:

- Businesses and organizations that need to manage multiple vendors.
- Teams looking for a customizable vendor management solution built on Laravel.
- Developers seeking a starting point for vendor-related applications.

## Features

- Vendor registration and profile management
- Vendor status tracking
- Search and filter capabilities
- Secure authentication and authorization
- Extensible architecture for custom requirements

## Installation

Follow these steps to install and set up the project:

1. **Clone the repository:**
    ```bash
    git clone https://github.com/rzki/mmg-vendor.git
    cd mmg-vendor
    ```

2. **Install dependencies:**
    ```bash
    composer install
    npm install && npm run dev
    ```

3. **Copy and configure environment file:**
    ```bash
    cp .env.example .env
    ```
    Update `.env` with your database and mail settings.

4. **Generate application key:**
    ```bash
    php artisan key:generate
    ```

5. **Run migrations:**
    ```bash
    php artisan migrate
    ```

6. **Start the development server:**
    ```bash
    php artisan serve
    ```

Visit `http://localhost:8000` in your browser to access the application.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
