<!DOCTYPE html>

<html>

<body>

<h2>Create an Account</h2>

<form action="process-signup.php" method="POST">

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