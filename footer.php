<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    include('includes/common.php');
    redirect_to('index', TRUE);
}

$siteid = get_setting(PIWIK_SITE_ID, FALSE);
?>
<!-- begin of footer.php -->
    <div class="white-box">
      <p class="footertext"><a href="#">coffeestats.org</a> is a project by <a href="http://dittberner.info">Jan Dittberner</a> &amp; <a href="http://noqqe.de">Florian Baumann</a> .<br/>
    </div>
  </div><!-- close content -->
</div><!-- close wrapper -->

<?php
if ($siteid !== NULL) {
    $http_url = get_setting(PIWIK_HTTP_URL);
    $https_url = get_setting(PIWIK_HTTPS_URL);
?>
<!-- Piwik -->
<script type="text/javascript">
    var pkBaseURL = (("https:" == document.location.protocol) ? "<?php echo $https_url; ?>" : "<?php echo $http_url; ?>");
    document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));

    try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", <?php echo $siteid; ?>);
        piwikTracker.trackPageView();
        piwikTracker.enableLinkTracking();
    } catch( err ) {}
</script>
<noscript><p><img src="<?php
    if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strcmp($_SERVER['HTTPS'], 'off') != 0)) {
        printf("%spiwik.php?idsite=%s", $https_url, $siteid);
    }
    else {
        printf("%spiwik.php?idsite=%s", $http_url, $siteid);
    }
?>" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->
<?php
}
?>

</body>
</html>
