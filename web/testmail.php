<?php
    $message = '
      <html><body style="font-family: Arial; font-size: 15px;">
      <p>You have registered for PsyPets with the login name mrr, however your account still needs to be activated!</p>

      <p>Your activation key is "gah" (without the quotes).</p>

      <p>To activate your account, visit <a href="http://www.psypets.net/activate.php">http://www.psypets.net/activate.php</a> and type in your login name and activation key, or use this link to do it automatically: <a href="http://www.psypets.net/activate.php?user=mrr&amp;activate=gah">http://www.psypets.net/activate.php?user=mrr&amp;activate=gah</a></p>

      <p>Once your account has been activated you will no longer need the activation key.</p>

      <p><center>&diams; &diams; &diams; &diams; &diams;</center></p>

      <p>PsyPets has an in-game mailing system.  You have been sent an introductory mail in-game which answers many of the most commonly asked questions, such as "How do I make money?", and explains how some of the basic game mechanics work.</p>

      <p>Please read this mail!</p>

      <p>Your in-game mail is found in your Mailbox, a link for which will be on the left of the screen once you have logged in.  There will also be an envelope icon in the upper-left, notifying you of unread mail, which can be clicked to take you to your Mailbox.</p>

      <p><center>&diams; &diams; &diams; &diams; &diams;</center></p>

      <p>Parents of young children may be interested in Content Control, as the public discussion boards are not always appropriate for all age groups!  After logging in, visit the "My Account" page (the link for which will be in the top-left area of the page).  From there, look for the "Content Control" section.</p>
      </body></html>
    ';

    mail('ben@telkoth.net', 'PsyPets account activation', $message, "MIME-Version: 1.0\nContent-type: text/html; charset=utf-8\nFrom: sender@psypets.net");
?>
