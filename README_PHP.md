# CourierPro - PHP/MySQL Implementation

A complete courier management system built with PHP and MySQL, featuring role-based access control for admins, agents, and customers.

## Features

### Admin Features
- **Dashboard**: Overview of all courier operations with statistics
- **Courier Management**: Add, edit, view, and delete courier shipments
- **Agent Management**: Manage courier agents and their assignments
- **Customer Management**: View customer accounts and order history
- **Reports & Analytics**: Generate various reports and export data
- **System Settings**: Configure company information and system preferences

### Agent Features
- **Dashboard**: View assigned couriers and performance metrics
- **Courier Updates**: Update courier status and location
- **Reports**: Generate agent-specific reports

### Customer Features
- **Package Tracking**: Track courier status with detailed timeline
- **Order History**: View past and current orders
- **Contact Support**: Get help and support

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5, jQuery
- **Authentication**: Session-based with password hashing
- **Security**: Input sanitization, SQL injection prevention

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for dependencies)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd courier-management
   ```

2. **Database Setup**
   ```bash
   # Create database and import schema
   mysql -u root -p
   CREATE DATABASE courier_management;
   exit
   
   # Import the schema
   mysql -u root -p courier_management < database/schema.sql
   ```

3. **Configure Database Connection**
   ```php
   // Edit config/database.php
   private $host = 'localhost';
   private $db_name = 'courier_management';
   private $username = 'your_username';
   private $password = 'your_password';
   ```

4. **Set Permissions**
   ```bash
   chmod 755 -R .
   chmod 777 uploads/ (if using file uploads)
   ```

5. **Access the Application**
   - Open your browser and navigate to `http://localhost/courier-management`
   - Use the default login credentials:
     - **Admin**: admin@courierpro.com / admin123
     - **Agent**: agent@courierpro.com / agent123
     - **Customer**: customer@courierpro.com / customer123

## Project Structure

```
courier-management/
├── config/
│   ├── database.php          # Database connection
│   └── config.php           # Application configuration
├── includes/
│   ├── auth.php            # Authentication functions
│   ├── functions.php       # Utility functions
│   ├── header.php          # Common header
│   ├── footer.php          # Common footer
│   └── sidebar.php         # Navigation sidebar
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── add-courier.php     # Add new courier
│   ├── couriers.php        # Courier management
│   ├── agents.php          # Agent management
│   ├── customers.php       # Customer management
│   ├── reports.php         # Reports & analytics
│   └── settings.php        # System settings
├── agent/
│   ├── dashboard.php       # Agent dashboard
│   └── couriers.php        # Agent courier management
├── customer/
│   ├── track.php          # Package tracking
│   └── orders.php         # Order history
├── assets/
│   ├── css/
│   │   ├── style.css      # Main styles
│   │   └── login.css      # Login page styles
│   └── js/
│       └── main.js        # JavaScript functionality
├── database/
│   └── schema.sql         # Database schema
├── index.php              # Main entry point
├── login.php              # Login page
└── logout.php             # Logout handler
```

## Database Schema

### Users Table
- Stores admin, agent, and customer accounts
- Role-based access control
- Password hashing for security

### Couriers Table
- Main courier shipment data
- Tracking numbers, sender/receiver info
- Status tracking and timestamps

### Courier Status History
- Tracks all status changes
- Location updates and notes
- Audit trail for deliveries

### Branches Table
- Courier service branches
- Agent assignments to branches

### Settings Table
- System configuration
- Company information
- Delivery preferences

## Security Features

1. **Authentication**
   - Session-based login system
   - Password hashing with PHP's password_hash()
   - Role-based access control

2. **Input Validation**
   - SQL injection prevention with prepared statements
   - XSS protection with htmlspecialchars()
   - Input sanitization functions

3. **Authorization**
   - Role-based page access
   - Function-level permissions
   - Session timeout handling

## API Endpoints (Future Enhancement)

The system is designed to easily add REST API endpoints:

```php
// api/couriers.php - Get courier information
// api/track.php - Track courier by number
// api/status.php - Update courier status
```

## Customization

### Adding New Features
1. Create new PHP files in appropriate directories
2. Add navigation items in `includes/sidebar.php`
3. Update database schema if needed
4. Add corresponding CSS/JS in assets folder

### Styling
- Modify `assets/css/style.css` for custom styling
- Bootstrap 5 classes available throughout
- Responsive design included

### Configuration
- Update `config/config.php` for application settings
- Modify `config/database.php` for database settings

## Deployment

### Production Deployment
1. **Security Hardening**
   ```php
   // Disable error reporting
   error_reporting(0);
   ini_set('display_errors', 0);
   
   // Use HTTPS
   // Update BASE_URL in config.php
   ```

2. **Database Optimization**
   ```sql
   -- Add indexes for better performance
   CREATE INDEX idx_tracking_number ON couriers(tracking_number);
   CREATE INDEX idx_status ON couriers(status);
   CREATE INDEX idx_created_at ON couriers(created_at);
   ```

3. **File Permissions**
   ```bash
   # Set proper permissions
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   chmod 600 config/database.php
   ```

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Permission Denied**
   - Check file permissions
   - Ensure web server has read access

3. **Session Issues**
   - Check PHP session configuration
   - Verify session directory permissions

### Debug Mode
Enable debug mode in `config/config.php`:
```php
define('DEBUG_MODE', true);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Email: support@courierpro.com
- Documentation: [Project Wiki]
- Issues: [GitHub Issues]

## Changelog

### Version 1.0.0
- Initial release
- Complete courier management system
- Role-based access control
- Responsive design
- Report generation