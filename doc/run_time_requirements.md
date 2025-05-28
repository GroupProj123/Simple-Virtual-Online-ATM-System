# Run-time Requirements

This document specifies the hardware and software environment required for the successful operation of the Simple Virtual Online ATM System, particularly focusing on deployment on a Raspberry Pi Zero 2 W for web service accessibility.

## 1. Hardware Platform

The developed system is intended to run on the following hardware platform:

* **Main Device:** Raspberry Pi Zero 2 W
* **Power Supply:** Certified USB Cable (for Raspberry Pi Zero 2 W)

## 2. Required Software Packages

The following software packages and their dependencies are required for running the application:

* **Operating System:** A compatible Linux distribution for Raspberry Pi, specifically **Raspberry Pi OS Lite (64-bit)** is recommended for optimal performance with web server and PHP.
* **Web Server:** Apache2 or Nginx.
    * **Purpose:** To serve the web application files (HTML, CSS, JavaScript, PHP) over HTTP.
* **Database Server:** MySQL or MariaDB.
    * **Purpose:** To store and manage user accounts, balances, and transaction history for the ATM system.
* **Programming Language:** PHP.
    * **PHP Version:** PHP 7.4 or higher (PHP 8.x recommended for better performance and features).
    * **Required PHP Extensions:**
        * `php-mysql` (or `php-mysqli`): Essential for PHP to connect and interact with the MySQL/MariaDB database.
        * `php-json`: For handling JSON data, which might be used in API responses or configurations.
        * `php-session`: Critical for managing user login sessions and states.
        * `php-fpm` (if using Nginx): FastCGI Process Manager is required for Nginx to process PHP scripts efficiently.
* **Web Browser:** Any modern web browser compatible with HTML5, CSS3, and JavaScript (e.g., Chromium, Firefox, Chrome, Edge) for client-side access.

## 3. Network Requirements

* **Internet Connection:** Required for initial setup, package installation, and system updates.
* **Local Network Access:** The system must be configured to be accessible over a local network.
* **WiFi Connectivity:** The Raspberry Pi Zero 2 W must be capable of connecting to **TKU WiFi** to obtain a network connection and a **public IP address** for external access to the web service during demonstration[cite: 1].

## 4. Database Setup

* A database named `atm_system` (or similar) needs to be created on the database server.
* Appropriate user accounts with necessary permissions (SELECT, INSERT, UPDATE, DELETE) for the `atm_system` database must be configured.
* The database schema (tables for users, transactions, etc.) must be imported/created as per the Database Design documentation.

## 5. Deployment and Configuration Guide (for RPi0 Assignment)

This section outlines the key configuration steps required to deploy the ATM web service on the Raspberry Pi Zero 2 W.

### a. Operating System Boot and Network Setup
* Ensure the Raspberry Pi Zero 2 W is properly set up to **boot into the installed OS** (e.g., Raspberry Pi OS Lite).
* Configure the Raspberry Pi to **connect to TKU WiFi**. This is crucial for enabling the device to have a network connection and obtain a public IP address, allowing external access to the web service[cite: 1].

### b. HTTP Server Installation and Configuration
* **Install** your chosen HTTP server (Apache2 or Nginx) on the Raspberry Pi.
* **Configure the HTTP server to allow HTTP access at port 80**[cite: 1]. This typically involves modifying the server's main configuration file (e.g., `httpd.conf` or `nginx.conf`) or a virtual host/server block configuration.
* **Define the default page** when the URL hits the root of the site (e.g., `index.php`, `index.html`). This ensures users land on the correct starting page when accessing the server's IP address or domain[cite: 1].

### c. Web Application Deployment
* **Publish your static web pages** (from previous homework) and PHP application files via the configured web server[cite: 1]. This usually means placing your project's files into the web server's document root directory (e.g., `/var/www/html/` for Apache or a custom path for Nginx). Ensure file permissions are set correctly for the web server to read them.
