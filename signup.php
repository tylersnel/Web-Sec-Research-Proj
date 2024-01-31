 <!DOCTYPE html>
  <html>
  <head>
  <meta charset="UTF-8">
  <title>Smaug Bank</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="app.css">
  </head>
  <body>

    <h2>Create an Account</h2>

    <!-- <form action="home.php" method="POST"> -->
    <form action="process_signup.php" method="POST">
    <!-- <form action="login.php" method="POST"> -->
      <fieldset>
        <legend>User information:</legend>
        User name:<br>
        <input type="text" name="user_name">
        <br>
        Password:<br>
        <input type="text" name="password">
        <!-- <input type="password" name="password"> -->
        <br>
        Repeat Password:<br>
        <input type="text" name="password_repeat">
        <!-- <input type="password" name="password_repeat"> -->
        <br>
        First Name on Account:<br>
        <input type="text" name="name">
        <br>
        <input type="submit" name="submit" value="submit">
      </fieldset>
    </form>

  </body>
  </html>
 