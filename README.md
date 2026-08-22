# Off the Pages

Off the Pages is a small PHP and MySQL blog application. Users can create an account, sign in, publish stories, browse community posts, and manage their own posts from a dashboard.

## Features

- User registration with hashed passwords
- Email and password login using PHP sessions
- Community blog listing ordered by newest post
- Individual blog post pages
- Authenticated users can create, edit, and delete their own posts
- Ownership checks prevent users from editing or deleting another user's posts
- Responsive pages with an editorial-style interface

## Requirements

- PHP 7.4 or later with the `mysqli` extension enabled
- MySQL or MariaDB
- Apache, such as the one included with XAMPP
- A browser with internet access for the hosted Google Fonts and Unsplash images used by the pages

## Project Structure

| File | Purpose |
| --- | --- |
| `index.php` | Authenticated home page with featured posts and navigation |
| `blogs.php` | Lists community blog posts |
| `blog.php` | Displays one blog post by ID |
| `register.php` | Creates a new user account |
| `login.php` | Authenticates a user and starts a session |
| `logout.php` | Ends the current session |
| `dashboard.php` | Shows the signed-in user's posts |
| `create_blog.php` | Creates a new blog post |
| `edit_blog.php` | Updates a post owned by the signed-in user |
| `delete_blog.php` | Deletes a post owned by the signed-in user |
| `db.php` | Creates the MySQLi database connection |

## Setup With XAMPP

1. Copy or clone this project into the XAMPP web root, for example:

   ```text
   C:\xampp\htdocs\Blog_application
   ```

2. Start **Apache** and **MySQL** from the XAMPP Control Panel.

3. Create the database configured in `db.php`, or update `db.php` with the credentials for your local database. Do not commit real database credentials to source control.

4. Create the required tables. The application expects the following columns:

   ```sql
   CREATE DATABASE blog;
   USE blog;

   CREATE TABLE user (
       id INT AUTO_INCREMENT PRIMARY KEY,
       username VARCHAR(100) NOT NULL UNIQUE,
       email VARCHAR(255) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL
   );

   CREATE TABLE blogpost (
       id INT AUTO_INCREMENT PRIMARY KEY,
       user_id INT NOT NULL,
       title VARCHAR(255) NOT NULL,
       content TEXT NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
       CONSTRAINT fk_blogpost_user
           FOREIGN KEY (user_id) REFERENCES user(id)
           ON DELETE CASCADE
   );
   ```

   If the database name or table structure differs from this example, keep the table and column names aligned with the SQL queries in the PHP files.

5. Open the application in a browser:

   ```text
   http://localhost/Blog_application/
   ```

## Typical Usage

1. Open the application and register an account.
2. Log in with the account's email and password.
3. Use **Create Post** to publish a title and story.
4. Browse posts from **Community Blogs**.
5. Use the dashboard to edit or delete posts belonging to the current account.
6. Use **Logout** to end the session.

## Authentication Behavior

The home page, dashboard, create, edit, delete, and individual post pages require an authenticated session and redirect to `login.php` when the session is missing. Community posts are listed by `blogs.php`.

## Configuration Notes

- Database connection settings are stored directly in `db.php`.
- Passwords are stored using PHP's `password_hash()` and checked with `password_verify()`.
- Prepared statements are used for user input in the database queries.
- The application currently enables PHP error display on several pages, which is useful during local development but should be disabled in production.
- The interface references external Google Fonts and Unsplash images, so those assets may not load without internet access.

## Security Before Deployment

- Move database credentials out of `db.php` and into environment-specific configuration.
- Disable `display_errors` and `display_startup_errors` in production.
- Add CSRF protection to state-changing forms and links.
- Use HTTPS and secure session-cookie settings.
- Validate and rate-limit authentication input before exposing the application publicly.
