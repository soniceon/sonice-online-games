# Sonice Online Games

A modern online gaming platform built with PHP, Twig, and MySQL.

## Features

- Modern and responsive design
- Game categories and filtering
- User authentication system
- Favorite games functionality
- Recently played games tracking
- Search functionality
- Mobile-friendly interface

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/sonice-online-games.git
cd sonice-online-games
```

2. Install dependencies:
```bash
composer install
```

3. Create the database:
```bash
mysql -u root -p < database/schema.sql
```

4. Configure your web server:

For Apache, create a `.htaccess` file in the `public` directory:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

For Nginx, add this to your server configuration:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

5. Update the database configuration in `config/database.php` with your credentials.

6. Make sure the `cache` directory is writable:
```bash
chmod 777 cache
```

## Directory Structure

```
.
├── config/             # Configuration files
├── database/          # Database schema and migrations
├── public/            # Public directory (web root)
│   ├── assets/       # Static assets (CSS, JS, images)
│   └── index.php     # Front controller
├── src/              # Application source code
├── templates/        # Twig templates
│   ├── components/  # Reusable components
│   ├── layouts/     # Layout templates
│   ├── pages/       # Page templates
│   └── partials/    # Partial templates
├── cache/           # Twig template cache
├── vendor/          # Composer dependencies
├── composer.json    # Composer configuration
└── README.md        # This file
```

## Development

To start the development server:

```bash
cd public
php -S localhost:8080
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details. 