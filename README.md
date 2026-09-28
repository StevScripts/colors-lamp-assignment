# COLORS

COLORS is a small web app from my COP 4331 LAMP lab. A user can log in,
add a color name, and search their saved colors. Colors are stored in MySQL,
so they are still there after closing the page and logging in again.

The lab site is at [colors.kirugames.com](https://colors.kirugames.com/).
This repository organizes the existing app for the version control assignment.

## Technologies

- Linux and Apache for the remote server
- MySQL for users and colors
- PHP with the `mysqli` extension for the API
- HTML, CSS, and JavaScript for the pages
- DigitalOcean for hosting the lab site

## Files

```text
api/          PHP connection helper and API endpoints
public/       HTML pages, CSS, JavaScript, and the background image
database/     Database table definitions
scripts/      Local development router and demo account script
.env.example  Blank database settings to copy for local use
```

The JavaScript sends JSON requests to `/LAMPAPI`. The PHP files live in `api/`
in this repository. The local router connects those URLs to the PHP files.

| Endpoint (POST) | JSON fields | Purpose |
| --- | --- | --- |
| `/LAMPAPI/Login.php` | `login`, `password` | Check an account and return the user's ID and name |
| `/LAMPAPI/AddColor.php` | `color`, `userId` | Save a color for that user |
| `/LAMPAPI/SearchColors.php` | `search`, `userId` | Find that user's colors containing the search text |

## Run locally

You need Git, MySQL 8, and PHP 8 with `mysqli` and `mysqlnd` enabled.
The commands below use a macOS or Linux terminal. Start MySQL before continuing.

1. Clone the repository and open its folder:

   ```sh
   git clone https://github.com/StevScripts/colors-lamp-assignment.git
   cd colors-lamp-assignment
   ```

2. Create an empty database called `COP4331`. For example, open MySQL with
   `mysql -u root -p` and run:

   ```sql
   CREATE DATABASE COP4331 CHARACTER SET utf8mb4;
   ```

   Exit MySQL with `exit;`, then import the tables:

   ```sh
   mysql -u root -p COP4331 < database/schema.sql
   ```

   Use your own MySQL administrator account if it is different from `root`.
   Create a separate local MySQL account for the app with `SELECT` and `INSERT`
   access to this database. Choose its password yourself and keep it private.

3. Copy the blank settings file:

   ```sh
   cp .env.example .env
   ```

   Edit `.env` with your database host, name, username, and password. The file
   uses shell syntax, so quote values containing spaces or special characters.
   `.env` is ignored by Git. PHP does not load it automatically; load it into
   the current terminal before running the next commands:

   ```sh
   set -a
   . ./.env
   set +a
   ```

4. Create a local demo login. Run the following commands one at a time.
   After `read`, type a password and press Enter. It will not appear on screen.
   Use a disposable password of 50 bytes or fewer for this lab account.

   ```sh
   read -r -s DEMO_PASSWORD
   export DEMO_PASSWORD
   php scripts/create-demo-user.php student
   unset DEMO_PASSWORD
   ```

   This creates the login `student` with the password you entered. No demo
   passwords or existing user records are included in this repository.

5. Start the development server from the repository folder:

   ```sh
   php -S 127.0.0.1:8000 -t public scripts/router.php
   ```

   Open [localhost:8000](http://127.0.0.1:8000/) in a browser. Log in with the
   account you just created. Opening the HTML file directly will not run PHP.
   Press Ctrl+C in the terminal to stop the server.

## Run on a LAMP server

Set up Linux, Apache, MySQL, and PHP with the MySQL extension. Import the schema
and create an app account as described above. Set Apache's document root to
`public/`, and map `/LAMPAPI` to the repository's `api/` directory with PHP
execution enabled. The local router is only needed for PHP's development server.

Provide `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` to PHP through the
server's private Apache or PHP-FPM configuration, then restart the affected
service. For a remote database, the MySQL app account must allow connections
from the web server. Run the demo account script with the same database settings
if an account is needed. Point a domain to the server and set up HTTPS to access
it remotely. Actual server configuration, keys, and credentials are not included.

## Check the app

1. Try an incorrect login and check that an error appears.
2. Log in with a valid account.
3. Add a color with a name you can easily recognize.
4. Close the tab, reopen the login page, and log in again.
5. Search for the color you added. It should still appear.

## Notes and limitations

- This is a class lab app. It compares passwords as plain text in the database.
  It needs password hashing before use with real accounts.
- The browser sends the user ID to the API. There is no server-side session
  verification, so this is not suitable for private user data.
- There is no registration page or password reset. Accounts are created separately.
- Color names have a 50-character database limit, and duplicate names are allowed.
- Searching with an empty field returns all colors for that user. A search with
  no matches shows `No Records Found`.
- The `Contacts` table comes from the lab schema but is not used by this app.

## Credits and AI use

The project is based on the course's COLORS LAMP starter files. OpenAI Codex
helped with server setup, PHP and JavaScript fixes, testing, organizing the
repository, and drafting this README.

## License

See [LICENSE.md](LICENSE.md) for the MIT license.
