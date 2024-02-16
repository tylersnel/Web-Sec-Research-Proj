## Steps and Instructions:

#### Injection:

- **SQL Injection:**
    1. Put `tyler' OR '1'='1` or `tyler'--` (note there is a space after --) in the username field.
    2. Enter anything in the password field.
    3. Login to access Tyler's account.

- **UNION SQL Injection:**
    1. Put `tyler' UNION ALL SELECT NULL, NULL, NULL, NULL, NULL FROM users--` (note there is a space after --) in the username field.
    2. Enter anything in the password field.
    3. No syntax error indicates the number of columns in the `users` table for intel gathering.

- **Error Based 1:**
    1. Put `'` in the username field.
    2. Enter anything in the password field.
    3. A syntax error will appear.
    4. Extract the error message to determine the database type.
    5. Error message also provides the location of the error in the query and file path.

- **Error Based 2:**
    1. Put `' OR 1=1;--` in the username field.
    2. Enter anything in the password field.
    3. A syntax error will appear.
    4. Error shows where the error occurred in the query, revealing column names like `password`.

#### Solution:
The first prevention technique used was the PHP function `mysqli_real_escape_string()` on user inputs. This function is used to escape special characters in a string when dealing with user input used in SQL queries. It adds escape characters before certain characters that have special meanings in SQL queries, ensuring the string can be safely used in an SQL query. Next, parametrized values and prepared statements were utilized for extra security.

Parametrized values are denoted by '?' (`$sql = "SELECT * FROM users WHERE user_name=? AND password=?";`) and separate SQL code from user input data. Later, prepared statements (`$stmt = $conn->prepare($sql);`) are used to bind values to these placeholders. Prepared statements separate SQL code from data. When these two features work in conjunction, they ensure user input is treated as data and not executable code.

## Additional Resources:
- [PHP: mysqli_real_escape_string - Manual](https://www.php.net/manual/en/mysqli.real-escape-string.php)
- [PHP: Prepared Statements - Manual](https://www.php.net/manual/en/mysqli.prepare.php)

## Broken-Access Control/ Authorization

- **Vertical privilege escalation:** where a user can gain admin privileges:
    - The direct access of the `/admin_home.php` in the URL is blocked by the sessions if-else conditional check `if (isset($_SESSION['id']) && isset($_SESSION['name']))`, and if they are not logged in, an automatic redirect to `/index.php` occurs.
    - The POST method in the forms prevents a user from identifying and modifying the information in the URL to gain access. The GET method could pass the user ID (or other information) where one individual could randomly insert numbers (brute force) that could access another account.

- **Horizontal privilege escalation:** where a user can access other users' data:
    - The POST method in the forms prevents a user from identifying and modifying the information in the URL to gain access. The GET method could pass the user ID (or other information) where one individual could randomly insert numbers (brute force) that could access another account.

- **Role-based access control (RBAC):** the user -> role -> permission -> capability. We have designed the application to limit the users' capability only to their own account. And admin login through a different pathway with different capabilities.
- **Discretionary Access Control (DAC):** admin capability is limited to admin and not any user. Admin can choose to edit a user's account information.
- **Integrity based or Mandatory Access Control (MAC):** where a central authority could regulate access. Banks normally keep records of all their activities, such as editing or deleting an account.

## Broken Authentication

- **Brute Force Password Guessing:** where an attacker gains access to a user account by guessing common passwords:
    - In our insecure site, users can create any password they wish. It can be a common password, 1 character, and no combinations required. Also, there are no limits to the number of attempts a user has on trying to access their account, leaving user accounts vulnerable to password guessing since the passwords could be commonly used and an attacker can keep guessing.
    - Test 1: Create a new user and create any password you wish.
    - Test 2: Attempt to log into new account with the wrong password as many times as you like. Then log into the account with the correct password immediately after.
    - Test 3: Login to username: tyler and password: 123. Common username and password.

- **Solution:**
    - In our secure site first, we added character requirements for passwords. They must be at least 12 characters long and must have 1 capital letter, 1 lowercase letter, 1 number, and 1 special character. Next, we check the potential user password against a list of 10k common passwords. If there is a match, the password is not accepted. We also implemented an account lock feature where if someone attempts 5 failed login accounts, the account is locked for 1 minute (short for grading purposes). The user can successfully log in again with the correct password after the 1-minute lockout expires.
    - Test 1: Try and create a new user with password 123.
    - Test 2: Try and create a new user with password qwertY12345^
    - Test 4: Create a new user following character requirements for password
    - Test 3: Step 1: Try to log into your newly created user 5 times with an incorrect password. 
             -Step 2: Try to log into your newly created account with the correct password.
             -Step 3: Once lockout expires, log into newly created user account. 

## Cryptographic Failures
## Insecure Design
## Security Misconfiguration
## Vulnerable and Outdated Components
