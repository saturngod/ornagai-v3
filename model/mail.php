<?php
use Mailgun\Mailgun;

class mail
{
  function send($from,$to,$subject,$message)
  {
    $mg = new Mailgun("key-f25bb8b658835d538bee77a279bd0f74");
    $domain = "msend.ornagai.com";


    # Now, compose and send your message.
    $mg->sendMessage($domain, array('from'    => $from,
                                    'to'      => $to,
                                    'subject' => $subject,
                                    'html'    => $message));
  }
}
