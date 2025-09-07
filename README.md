# IWP Demo - Setup Guide
## 1. Install XAMPP for Windows

1. Go to the official XAMPP website: https://www.apachefriends.org/index.html
2. Download the XAMPP installer for Windows.
3. Run the installer and follow the setup instructions.
4. After installation, open the XAMPP Control Panel and start the Apache and MySQL modules.

## 2. SQL Database Setup

1. Open XAMPP Control Panel and click 'Admin' next to MySQL to open phpMyAdmin.
2. In phpMyAdmin, click 'New' to create a new database. Name it `IWP`.
3. Go to the SQL tab and run the following query to create the `users` table:

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);
```

4. (Optional) Add some sample data if you want:

```sql
INSERT INTO users (name, email) VALUES ('A', 'A@email.com'), ('B', 'B@email.com');
```

## 3. Project Setup & Running

1. Copy all project files (including `.env`, `Main.php`, and `README.md`) into a folder inside `C:/xampp/htdocs/` (e.g., `C:/xampp/htdocs/IWPProjectWork/`).
2. Edit the `.env` file with your database credentials if needed:

```
servername = "localhost"
username = "root"
password = "your_mysql_password"
dbname = "IWP"
```

3. Open your browser and go to: `http://localhost/IWPProjectWork/Main.php`
4. You should see the Database Manager interface. You can add new records and view the table.

## 4. Troubleshooting

- If you see a connection error, check your `.env` credentials and make sure MySQL is running in XAMPP.
- If you get a table not found error, make sure you created the `users` table in the correct database.
- For any PHP errors, check your XAMPP installation and make sure all files are in the correct folder.

---

**This demo is ready to use and portable. Just follow the steps above to run it on any Windows machine with XAMPP installed!**
