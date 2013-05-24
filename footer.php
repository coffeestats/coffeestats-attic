<?php
if (strcmp($_SERVER['SCRIPT_FILENAME'], __FILE__) == 0) {
    include('includes/common.php');
    redirect_to('index', TRUE);
}

$siteid = get_setting(PIWIK_SITE_ID, FALSE);
?>
<!-- begin of footer.php -->
    <div class="footer">
      <p class="footertext"><a href="#">coffeestats.org</a> is a project by <a href="http://dittberner.info">Jan Dittberner</a> &amp; <a href="http://noqqe.de">Florian Baumann</a>.
      See <a href="/imprint">Imprint</a>.</p>
    </div>
  </div><!-- close content -->
</div><!-- close wrapper -->

<script type="text/javascript" src="/lib/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('.flash-info').delay(4000).fadeOut(1000, function() { this.remove(); });
    $('.flash-success').delay(4000).fadeOut(1000, function() { this.remove(); });
    $('li a.close').click(function(event) {
        $(this).parent().fadeOut(1000, function() { this.remove(); });
    });
});
</script>
<?php
if ($siteid !== NULL) {
    $piwikhost = get_setting(PIWIK_HOST);
?>
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://<?php echo $piwikhost; ?>/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "<?php echo $siteid; ?>"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->
<?php
}
?>
</body>
</html>
