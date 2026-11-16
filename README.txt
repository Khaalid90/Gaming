GAMEHUB - XAMPP starter project
================================

What it does
- Public home page with searchable game listings
- Game detail page
- Download counter
- One-time admin account setup
- Admin login and dashboard
- Upload games (ZIP, EXE, APK, ISO) with title, description and cover image selected from your phone (JPG, PNG, WEBP up to 5 MB) or a direct image URL
- PHP, CSS, JavaScript and SQL are kept in separate files

IMPORTANT
Only upload games you created yourself or have permission to distribute. Do not use this site to share pirated or copyrighted games without permission.
For safety, uploads are stored outside the public web folder and served through download.php.
This is a learning/demo starter. Before putting it online, add HTTPS, backups, rate limits, malware scanning, and stronger operational security.

INSTALL ON XAMPP
1. Extract the gamehub_xampp folder into:
   C:\xampp\htdocs\
   (You should then have C:\xampp\htdocs\gamehub_xampp\index.php)

2. Start Apache and MySQL from the XAMPP Control Panel.

3. Open http://localhost/phpmyadmin
   Create/import the database by selecting Import and choosing database.sql.
   The SQL creates a database named gamehub_db and its tables.

4. Open config/database.php and check the database settings.
   Default XAMPP settings are usually:
   host=localhost, username=root, password is blank.

5. Create the private uploads folder:
   C:\xampp\gamehub_storage
   (This is outside htdocs, so uploaded files cannot be accessed directly.)

6. Open:
   http://localhost/gamehub_xampp/setup_admin.php
   Create your admin username and password. This page disables itself after the first admin is created.

7. Visit:
   http://localhost/gamehub_xampp/
   Log in through Admin to upload games.

PHP SETTINGS
The project checks the PHP upload limit. To allow larger files, edit C:\xampp\php\php.ini:
  upload_max_filesize = 500M
  post_max_size = 510M
  max_execution_time = 300
  max_input_time = 300
Then restart Apache. Set limits appropriate to your computer and hosting plan.

SUPPORTED UPLOADS
.zip, .exe, .apk, .iso
The server validates file extension and MIME type, but MIME detection differs by system.
For a public website, do not allow executable files unless you have a strong reason and security review.
