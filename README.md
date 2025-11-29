# HelpDesk System

## Overview
The HelpDesk System is a web application designed to facilitate support ticket management for users, support staff, and administrators. It allows users to submit tickets, track their status, and enables support staff to manage and respond to these tickets. Administrators have additional capabilities to manage users and view statistics.

## Features
- User authentication (login and registration)
- Role-based access control (Administrators, Support Staff, Registered Users)
- Ticket submission and management
- Admin dashboard for user and ticket management
- Responsive design for user interface

## Technologies Used
- PHP
- MySQL
- Docker
- Nginx
- HTML/CSS/JavaScript

## Installation Instructions

### Prerequisites
- Docker and Docker Compose installed on your machine.

### Setup
1. Clone the repository:
   ```
   git clone <repository-url>
   cd helpdesk-system
   ```

2. Copy the environment file:
   ```
   cp .env.example .env
   ```

3. Update the `.env` file with your database credentials.

4. Build and run the Docker containers:
   ```
   docker-compose up --build
   ```

5. Initialize the database:
   - Access the MySQL container:
     ```
     docker exec -it <mysql-container-name> bash
     ```
   - Run the SQL initialization script:
     ```
     mysql -u root -p < /path/to/init.sql
     ```

6. Access the application:
   - Open your web browser and navigate to `http://localhost`.

## Usage
- **User Registration**: New users can register through the registration page.
- **User Login**: Registered users can log in to submit and track their tickets.
- **Ticket Management**: Users can create tickets, and support staff can view and manage them.
- **Admin Dashboard**: Administrators can manage users and view ticket statistics.

## Contributing
Contributions are welcome! Please submit a pull request or open an issue for any enhancements or bug fixes.

## License
<<<<<<< HEAD
This project is licensed under the MIT License. See the LICENSE file for details.
=======
This project is licensed under the MIT License. See the LICENSE file for details.
>>>>>>> e4d093afed88afddfc080d25b590a61193038726
