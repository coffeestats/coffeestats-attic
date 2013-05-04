<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    header('Status: 301 Moved Permanently');
    header('Location: index');
    exit();
}
?>
<!-- begin of footer.php -->
    <div class="white-box">
      <p class="footertext"><a href="#">coffeestats.org</a> is a project by Holger Winter &amp; <a href="http://noqqe.de">Florian Baumann</a> .<br/>
      Follow us on <a href="http://www.facebook.com/pages/coffeestatsorg/135455386573798?sk=info">Facebook</a>! (We won't keep you from clicking "I like" either!)</p>
    </div>
  </div><!-- close content -->
</div><!-- close wrapper -->

<!-- Piwik -->
<script type="text/javascript">
    var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.n0q.org/" : "http://piwik.n0q.org/");
    document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));

    try {
        var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 6);
        piwikTracker.trackPageView();
        piwikTracker.enableLinkTracking();
    } catch( err ) {}
</script>
<noscript><p><img src="http://piwik.n0q.org/piwik.php?idsite=6" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Code -->

</body>
</html>
