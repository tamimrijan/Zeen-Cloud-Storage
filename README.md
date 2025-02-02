# Zeen-Cloud-Storage

# Simple Cloud Storage

A simple cloud storage web application that allows users to upload, download, and manage files. Built using PHP, MySQL, HTML, CSS, and JavaScript, this project is designed to run on XAMPP.

## Features
- User authentication (signup, login, logout)
- Upload and download files
- Create and manage folders
- Modern and responsive UI
- Secure file storage with database integration

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP, MySQL
- **Server**: XAMPP

## Installation

1. Clone this repository:
   ```bash
   git clone https://github.com/your-username/cloud-storage.git
   ```
2. Move the project to the XAMPP `htdocs` directory.
3. Start Apache and MySQL from XAMPP Control Panel.
4. Import the database:
   - Open `phpMyAdmin`
   - Create a new database (e.g., `cloud_storage`)
   - Import `database.sql` file from the project
5. Configure database connection in `config.php`:
   ```php
   $host = 'localhost';
   $user = 'root';
   $password = '';
   $database = 'cloud_storage';
   ```
6. Open your browser and go to:
   ```
   http://localhost/cloud-storage
   ```

## Usage
- Register a new account or log in.
- Upload files and create folders.
- Manage your stored files securely.

## Screenshots
(Include screenshots of the project here)

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License
This project is licensed under the MIT License.

---
Feel free to update this README with more details as your project evolves!
