<?php
include('includes/common.php');

if (isset($_GET['u'])) {
    redirect_to(sprintf('profile?u=%s', $_GET['u']), TRUE);
}

errorpage('Bad Request', 'The request you sent was not correct.', '400 Bad Request');
?>
