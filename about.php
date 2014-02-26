<?php
include('auth/lock.php');
include("header.php");
?>
<div class="white-box">
    <h2>About coffeestats.org</h2>
    <p>Coffeestats.org was written with the help of two Mac OS X systems, a
    Debian GNU/Linux system and is proudly running on
    <a href="http://openbsd.org">OpenBSD</a>. We want to thank the awesome Free 
    Software community for all their software and art available under the
    <a href="http://creativecommons.org">CreativeCommons</a> license:</p>

    <ul>
        <li><a href="http://chartjs.org">Chart.js</a> by Nick Downie</li> 
        <li><a href="http://www.dafont.com/harabara.font">Harabara</a> by André Harabara on dafont.com</li>
        <li><a href="http://adamwhitcroft.com/batch/">Batch Iconset</a> by Adam Whitcroft</li>
        <li><a href="http://vervex.deviantart.com/art/Somacro-32-300DPI-Social-Media-Icons-267955425">Somacro Social Media Icons</a> by <a href="http://veodesign.com">Vervex</a></li>
    </ul>
    <p>Follow us on <a href="https://twitter.com/coffeestats">Twitter</a> or
    <a href="http://www.facebook.com/pages/coffeestatsorg/135455386573798?sk=info">Facebook</a>!
    (We won't keep you from clicking "I like" either!)</p>

</div>
<div class="white-box">
    <h2>Changelog</h2>
    <ul>
        <li>2014-02-20 REST API for add drinks by Clemens</li>
        <li>2013-05-23 New Design by Jeremias</li>
        <li>2013-05-07 Added nice social media icons to profile page</li>
        <li>2013-05-03 Public URL has changed (update your bookmarks!)</li>
        <li>2013-04-28 Performance and Security improvments</li>
        <li>2013-03-23 New Update page</li>
        <li>2013-03-23 Using Chart.js for Graphs!</li>
        <li>2013-03-23 Added Mate to drinks</li>
        <li>2013-02-15 Added more Ranking stuff onto the explore page</li>
        <li>2013-02-27 Some bugfixes for OTR</li>
        <li>2013-02-15 Max email length resized up to 50 chars</li>
        <li>2013-02-15 Made time for coffee +1 with js not servertime. Better feeling for users who not live in GMT+1</li>
        <li>2013-02-13 Added some awesome icons to navigation and cleaned up interface</li>
        <li>2012-02-11 Added SSL <a href="https://coffeestats.org">https://coffeestats.org</a></li>
        <li>2012-11-06 Migrated from Debian Squeeze to OpenBSD</li>
        <li>2012-02-11 Added on-the-run Mode</li>
        <li>2012-01-23 Added Overall Stats</li>
    </ul>
</div>
<div class="white-box">
    <h2>Credits</h2>
    <p>Some awesome guys have been involved in growing up coffeestats.org so far.</p>
    <ul>
        <li><a href="http://github.com/neverpanic">Clemens Lang</a> (implementing add-drink REST API)</li>
        <li><a href="http://www.art-ifact.de/">Jeremias Arnstadt</a> (new design)</li>
        <li><a href="http://dittberner.info">Jan Dittberner</a> (massive code improvements in many cases)</li>
        <li><a href="http://sotiriu.de">Nikolas Sotiriu</a> (testing the site for security flaws)</li>
        <li><a href="http://noqqe.de">Florian Baumann</a> (initial development)</li>
        <li>Holger Winter (page design/layout)</li>
    <ul>
</div>
<div class="white-box">
    <h2>Contribution</h2>
    <p>coffeestats.org is a OpenSource. Feel free to <a href="https://github.com/coffeestats/coffeestats">contribute code</a>, 
      <a href="https://github.com/noqqe/devnull-as-a-service/issues?state=open">share your ideas or report bugs</a>!</p>
    <p>Our documentation is at <a href="https://coffeestats.readthedocs.org">coffeestats.readthedocs.org</a>


    <p>Or show some love &lt;3</p>
    <p><script id='fb1dxpt'>(function(i){var f,s=document.getElementById(i);f=document.createElement('iframe');f.src='//api.flattr.com/button/view/?uid=noqqe&title=coffeestats.org&url=https%3A%2F%2Fcoffeestats.org';f.title='Flattr';f.height=62;f.width=55;f.style.borderWidth=0;s.parentNode.insertBefore(f,s);})('fb1dxpt');</script></p>
</div>

<?php
include("footer.php");
?>
