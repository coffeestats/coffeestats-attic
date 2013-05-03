<?php
if (isset($_GET['u'])) {
    header("Status: 301 Moved Permanently");
    header(sprintf('Location: profile?u=%s', $_GET['u']));
    exit();
}
header("Status: 400 Bad Request");
include('preheader.php');
?>
<div class="white-box">
    <h2>Bad Request</h2>
    <p>The request you sent was not correct.</p>
</div>
<?php
include('footer.php');
?>
