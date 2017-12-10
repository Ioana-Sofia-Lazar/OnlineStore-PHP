<?php    

require_once "Mail.php";

$from = "Sandra Sender <yoyogirl9596@yahoo.com>";
$to = "Ramona Recipient <yoyogirl9596@yahoo.com>";
$subject = "Hi!";
$body = "Hi,\n\nHow are you?";

$host = "smtp.gmail.com";
$username = "ioana.pascu9596@gmail.com";
$password = "Uknowulovem3";

$headers = array ('From' => $from,
  'To' => $to,
  'Subject' => $subject);
$smtp = Mail::factory('smtp',
  array ('host' => $host,
    'auth' => true,
    'username' => $username,
    'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
  echo("<p>" . $mail->getMessage() . "</p>");
 } else {
  echo("<p>Message successfully sent!</p>");
 }


?>