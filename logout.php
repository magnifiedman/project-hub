<?php
setcookie('userLogged','',-3600);
setcookie('userID','',-3600);
setcookie('userLevel','',-3600);
setcookie('evenotify','',-3600);
setcookie('evernote_email','',-3600);
header("Location: index.php");
