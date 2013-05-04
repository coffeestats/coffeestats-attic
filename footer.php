<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    include('includes/common.php');
    redirect_to('index', TRUE);
}
?>
<!-- begin of footer.php -->
    <div class="white-box">
      <p class="footertext"><a href="#">coffeestats.org</a> is a project by <a href="http://dittberner.info">Jan Dittberner</a> &amp; <a href="http://noqqe.de">Florian Baumann</a> .<br/>
    </div>
  </div><!-- close content -->
</div><!-- close wrapper -->

<?php
// TODO: move piwik configuration to config file (see https://bugs.n0q.org/view.php?id=14)
?>
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
