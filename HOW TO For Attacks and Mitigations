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

## Broken-Access Control
- Vertical privilege escalation: where a user can gain admin privileges
- Horizontal privilege escalation: where a user can access other users' data

## Cryptographic Failures
## Insecure Design
## Security Misconfiguration
## Vulnerable and Outdated Components
