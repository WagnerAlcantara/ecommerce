<?php

namespace Hcode;

use Rain\Tpl;
//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

Class Mailer{

  const USERNAME = 'contato@patudinhos.com.br';
  const PASSWORD = 'Bobesponja123';
  const NAME_FROM = 'Hcode_store';

  private $email;

  public function __construct( $to, $name, $subject, $layout, $data = array() )
  {

      $config = array(
        "tpl_dir"       => $_SERVER['DOCUMENT_ROOT'].'/views/email/',
        "cache_dir"     => $_SERVER['DOCUMENT_ROOT']."/views-cache/",
        "debug"         => false // set to false to improve the speed
        );

      Tpl::configure( $config );

      $tpl = new Tpl;

      foreach ($data as $key => $value) {
        $tpl->assign($key, $value);
      }

      $html = $tpl->draw($layout, true);

      //Create a new PHPMailer instance
      $this->mail = new PHPMailer;

      //Tell PHPMailer to use SMTP
      $this->mail->isSMTP();

      //Enable SMTP debugging
      // SMTP::DEBUG_OFF = off (for production use)
      // SMTP::DEBUG_CLIENT = client messages
      // SMTP::DEBUG_SERVER = client and server messages
      $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;

      //Set the hostname of the mail server
      $this->mail->Host = 'mail.patudinhos.com.br';
      // use
      // $this->mail->Host = gethostbyname('smtp.gmail.com');
      // if your network does not support SMTP over IPv6

      //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
      $this->mail->Port = 587;

      //Set the encryption mechanism to use - STARTTLS or SMTPS
      $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

      //Whether to use SMTP authentication
      $this->mail->SMTPAuth = true;

      //Username to use for SMTP authentication - use full email address for gmail
      $this->mail->Username = Mailer::USERNAME;

      //Password to use for SMTP authentication
      $this->mail->Password = Mailer::PASSWORD;

      //Set who the message is to be sent from
      $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

      //Set an alternative reply-to address
      //$this->mail->addReplyTo('replyto@example.com', 'First Last');

      //Set who the message is to be sent to
      $this->mail->addAddress($to, $name);

      //Set the subject line
      $this->mail->Subject = $subject;

      //Read an HTML message body from an external file, convert referenced images to embedded,
      //convert HTML into a basic plain-text alternative body
      $this->mail->msgHTML($html);

      //Replace the plain text body with one created manually
      $this->mail->AltBody = 'This is a plain-text message body';

      //Attach an image file
      //$this->mail->addAttachment('images/phpmailer_mini.png');
  }

  public function send()
  {

     //send the message, check for errors
     if (!$this->mail->send()) {
      echo 'Mailer Error: '. $this->mail->ErrorInfo;
    } else {
      echo 'Message sent!';
      //Section 2: IMAP
      //Uncomment these to save your message in the 'Sent Mail' folder.
      #if (save_mail($this->mail)) {
      #    echo "Message saved!";
      #}
  }

  }


}