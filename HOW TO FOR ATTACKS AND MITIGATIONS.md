## Table of Contents 
- [Injection](#injection)
- [Broken-Access Control / Authorization](#broken-access-control--authorization)
- [Broken Authentication](#broken-authentication)
- [Cross Site Scripting (XSS)](#cross-site-scripting-xss)
- [Server-Side Request Forgery (SSRF)](#server-side-request-forgery-ssrf)

## IMPORTANT: For insecure and secure site, make sure you are logged into OSU VPN as the site is hosted on the school's web servers. The site will not work if not logged in! Start the site in a new browser each time the insecure or secure sites are used.

## Steps and Instructions:

#### Injection:
- **Reason for Successful Attack** The reason SQL injection is successful on our insecure website is that we lack input validation. Whatever the attacker manually inputs into the user input fields is used directly in the SQL queries. Thus an attacker can manipulate our SQL queries to try and login without knowing the password, gather information for more complex attacks, or anything else they are looking to accomplish.

- **SQL Injection:**
    - Test: On our insecure site, put `tyler'-- ` (note there is a space after --) in the username field. Enter anything in the password field. Login to access Tyler's account.

- **UNION SQL Injection:**
    - Test: On our insecure site, put `tyler' UNION ALL SELECT NULL, NULL, NULL, NULL, NULL FROM users-- ` (note there is a space after --) in the username field. Enter anything in the password field. You will see this error "tyler' UNION ALL SELECT NULL, NULL, NULL, NULL, NULL FROM users-- username not found or 123 password incorrect. Try again". Now try `tyler' UNION ALL SELECT NULL, NULL, NULL, NULL FROM users-- `(note there is a space after --) in the username field. No error message will appear.  From this, we can tell how many columns are in the table (5) from the message that displays as it is not a syntax error. Syntax errors do not appear on the hosted site since it is on OSU's web servers. 

#### Solution:
The first prevention technique used was the PHP function `mysqli_real_escape_string()` on user inputs. This function is used to escape special characters in a string when dealing with user input used in SQL queries. It adds escape characters before certain characters that have special meanings in SQL queries, ensuring the string can be safely used in an SQL query. Next, parametrized values and prepared statements were utilized for extra security.

Parametrized values are denoted by '?' (`$sql = "SELECT * FROM users WHERE user_name=? AND password=?";`) and separate SQL code from user input data. Later, prepared statements (`$stmt = $conn->prepare($sql);`) are used to bind values to these placeholders. Prepared statements separate SQL code from data. When these two features work in conjunction, they ensure user input is treated as data and not executable code.

- Test 1: On our more secure site, put `tyler'-- ` (note there is a space after --) in the username field. Enter anything in the password field. You should not be allowed in.
- Test 2: On our more secure site, put `tyler' UNION ALL SELECT NULL, NULL, NULL, NULL, NULL FROM users-- ` (note there is a space after --) in the username field. Enter anything in the password field. You will now see this error "tyler\' UNION ALL SELECT NULL, NULL, NULL, NULL, NULL FROM users-- username not found. Try again". Now try `tyler' UNION ALL SELECT NULL, NULL, NULL, NULL FROM users-- `(note there is a space after --) in the username field. An error message will now appear and thus you will not be able to gather information about the database. 

## Additional Resources:
- [PHP: mysqli_real_escape_string - Manual](https://www.php.net/manual/en/mysqli.real-escape-string.php)
- [PHP: Prepared Statements - Manual](https://www.php.net/manual/en/mysqli.prepare.php)

## Broken-Access Control / Authorization

- **Reason for Successful Attack**
The reason for the successful vertical privilege escalation attack is the flawed conditional checks in the session management system. Specifically, the system allows direct access to the admin section (/admin_home.php) if certain session variables related to regular user authentication are set, without verifying the user's admin privileges. Another reason for a successful attack is the use of GET requests in forms instead of POST.

- **Attacks:**
**Test 1:** 
- **Vertical privilege escalation:** where a user can gain admin privileges:
    - Exploit the vertical privilege escalation vulnerability by directly accessing `/admin_home.php` while being logged in as a regular user. This can be achieved by manipulating the URL to bypass the admin check or the session variables. The direct access of the `/admin_home.php` in the URL is allowed by the sessions if-else conditional `if (isset($_SESSION['id']) && isset($_SESSION['name']))` and blocked with `if (isset($_SESSION['admin_id']) && isset($_SESSION['admin_name']))`, and if they are not logged in, an automatic redirect to `/index.php` occurs.
    - The GET method in the admin login form allows a user to randomly insert the name and pwd fields by brute force to access the admin account.
**Test 2:** 
- **Horizontal privilege escalation:** where a user can access other users' data:
    - In the insecure application, manipulate the GET parameters in the URL to access other users' data through brute force by trying different user name and password in the URL to access accounts of other users.

- **Solution:**
To mitigate the vertical privilege escalation vulnerability, the access to admin functionalities is strictly restricted to users with appropriate admin privileges. This can be achieved by implementing proper access control mechanisms, such as role-based access control (RBAC) through sessions. The secure site uses the POST method in the admin login forms to prevent a user from identifying and modifying the information in the URL to gain access. To address horizontal privilege escalation, the secure site uses the POST method in the forms to prevent a user from identifying and modifying the information in the URL to gain access.


## Broken Authentication
- **Reason for Successful Attack** When there are no password requirements, people will tend to use passwords that are easier to remember and if they are easy to remember, they are easy to guess. Also, if there is no limit on the number of attempts one can try on a login attempt, then an attacker can just keep trying until they get it right. No requirements for passwords and no limit on the number of attempts to login leaves user accounts vulnerable to attack from a persistent attacker. 

- **Brute Force Password Guessing:** where an attacker gains access to a user account by guessing common passwords:
    - On our insecure site, users can create any password they wish. It can be a common password, 1 character, and no combinations required. Also, there are no limits to the number of attempts a user has on trying to access their account, leaving user accounts vulnerable to password guessing since the passwords could be commonly used and an attacker can keep guessing.
    - Test 1: From the login page, go to the create a new user page by clicking on the sign up here link. Create a new user with any password you wish.
    - Test 2: Go back to the login page by clicking the return link. Attempt to log into the new account with the wrong password as many times as you like as you will not lock the account. Then log into the account with the correct password immediately after.
    - Test 3: Log out and navigate back to the login page. Login to username: tyler and password: 123. Common username and password to guess.
    - Test 4: Download brute_force_login_script.py and 10k-most-common.txt. Make appropriate edits to Python script. Where to edit is marked in the script. Run script to brute force common passwords. If the script is able to log in, the successful password will be printed to the terminal.

- **Solution:**
    - In our secure site first, we added character requirements for passwords. They must be at least 12 characters long and must have 1 capital letter, 1 lowercase letter, 1 number, and 1 special character. Next, we check the potential user password against a list of 10k common passwords. If there is a match, the password is not accepted. We also implemented an account lock feature where if someone attempts 5 failed login accounts, the account is locked for 1 minute (short for grading purposes). The user can successfully log in again with the correct password after the 1-minute lockout expires.
    - Test 1: On the more secure site's login page, navigate to the create a user page by clicking the sign up here link. Try and create a new user with password 123.
    - Test 2: Try and create a new user with password qwertY12345
    - Test 3: Create a new user following the character requirements for the password.
    - Test 4: Step 1: Try to log into your newly created user 5 times with an incorrect password. 
             -Step 2: Try to log into your newly created account one more time but with the correct password. Your account should be locked for one minute. 
             -Step 3: Once lockout expires, log into a newly created user account with the correct password.

## Cross Site Scripting (XSS)
- **Reason for Successful Attack** The reason an XSS attack is successful on our insecure website is due to the lack of proper sanitization and encoding of user input before displaying it back to the user. For example in our insecure site, if a user input username is not found or the password is incorrect, that user input username and password is displayed back to the user with a warning that the username could not be found or the password is incorrect. Thus, an attack would be able to add an XSS attack to the username or password input field.

- **Attacks:**
    - Test 1: On our insecure site,  log in to your account or create one if you have not yet. Navigate to the Pay Bills page. In the TO section, input `<script>alert("XSS")</script>` and complete the rest of the inputs and click Submit. An alert should appear warning of an XSS attack.
    - Test 2: Log out of your account. From the login page, click on the sign up here tag. In the user name field, input `<script>alert("XSS attack")</script>` and fill out the rest of the input fields as you wish. An alert should appear warning of an XSS attack.
    - Test 3: Navigate back to the login page and click on the link to the admin page and log in as the adimn. (username and password are admin). Once the admin page loads, an XSS attack alert should appear. 
   

- **Solution:**
    - To fix XSS vulnerability, we need to properly sanitize and encode any user-generated content before echoing it back to the HTML response. To do that, we decided to use the function `htmlspecialchars()` in PHP to encode special characters and prevent them from being interpreted as HTML or JavaScript. Using that function, any HTML tags or special characters being displayed back to the user will be properly escaped, reducing the risk of XSS attacks.
    - Test 1: On our more secure site, once logged in to your account, navigate to the Pay Bills page. In the TO section, input `<script>alert("XSS")</script>` and complete the rest of the inputs and click Submit. No alert should appear.
    - Test 2: Sign out and go back to the login page, click on the sign up here tag. In the user name field, input `<script>alert("XSS attack")</script>` and fill out the rest of the input fields as you wish.  No alert should appear and a new account will be created.
    - Test 3: Navigate to the administrator page from the login page. (username and password are admin). The main page should load without an XSS attack warning. 
    

## Server-Side Request Forgery (SSRF)

- **Reason for Successful Attack:**
Server-Side Request Forgery (SSRF) is a vulnerability that occurs when an attacker manipulates the server into making unauthorized requests to internal or external resources. In our application, the SSRF vulnerability arises from inadequate validation and sanitization of user-input URLs utilized in server-side requests. The SSRF attack succeeds due to the absence of proper input validation and sanitization of the URL parameter used in server-side requests. Our unsecure application allows users to input URLs directly into the API request, which are then utilized in server-side requests without adequate validation or restriction, enabling attackers to manipulate the API parameter for unauthorized server-side requests.

- **Attacks:**
   - The SSRF vulnerability can also be exploited through the request from the Yahoo Finance button. For the unsecure site request, input `https://web.engr.oregonstate.edu/~snelgrot/delete_user.php?id=1` into the stock API field intended to fetch external content. This URL will delete that user in the database.  The number for the id can be variable and only works if the user exists.
   - It is possible for an attacker to also enter other sensitive areas of the application or to input a malicious site.

- **Solution:**
To mitigate SSRF vulnerabilities, input validation and whitelisting are used on the server-side to restrict the URLs that can be exchanged in the request, which the whitelist of the allowed domain is "yahoo-finance127.p.rapidapi.com". Implementing a whitelist of the allowed domain to external resources mitigates this SSRF attack. On our secure site, input `https://web.engr.oregonstate.edu/~chapmaj2/delete_user.php?id=1`(or whichever user id exists) into the API request field intended for fetching external content. The request should be rejected, demonstrating the intended mitigation measures.
