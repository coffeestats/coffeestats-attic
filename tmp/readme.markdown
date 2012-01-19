An epic idea is born.



### HOLGER ###################################

° Register-Link wieder auf die Login-Seite einbauen
° #footer !!!!1111einseinself
* Boxen groesser machen fuer #content
* 1 column layout fuer index uws

### FLO ######################################

* Kaffee nachtragen können mit Timestampangabe

if (preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $timestamp)) 
{
      // Timestamp.
} 
else 
{
      // Not a timestamp.
}

* Uservergleich "compare" angabe Useraccount in POST und dropdown menü 
für daily, monthly, yearly. 

* Public User profile coffeestats.org/user/noqqe rewrite einrichten


* Settings Page bauen. 

* Suche bauen mit quickstats for each user

* plusone.php check ob timestamp schon 15 min alt
 SELECT user_id from mw_user where TIMEDIFF(CURRENT_TIME(), '10:50') >
 '00:15:00' order by user_id limit 1;
* Pure Stats 
SELECT DATEDIFF(CURRENT_DATE(), '2011-01-19');
$sql="select count(cid) as coffees, from cs_coffees where cuid = '".$profileid."'; ";

* Besseres Password encrypting
$hash = crypt('rasmuslerdorf', '$2a$07$usesomesillystringforsalt$');
