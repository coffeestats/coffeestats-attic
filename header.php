<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="author" content="F. Baumann, H. Winter" />
<meta name="description" content="coffeestats.org | All about coffee" />
<title>coffeestats.org</title>
<link rel="stylesheet" type="text/css" href="../css/caffeine.css" />
</head>
<body>

<div id="wrapper">

	<div id="header">
        <h1>coffeestats.org</h1>
        	<p>...about what keeps you awake at night.</p>
	</div>

	<div id="content">

		<div class="white-box">
			<div id="navigation">
				<ul id="navigation">
					<li><a href="index"><img src="../images/home.png"> Home</a></li>
					<li><a href="plusone"><img src="../images/tag.png"> Update</a></li>
                    <li><a href="profile?u=<?php echo $_SESSION['login_user']; ?>"><img src="../images/profile.png"> Profile</a></li>
					<li><a href="explore"><img src="../images/list.png"> Explore</a></li>
					<li><a href="overall"><img src="../images/flask.png"> Overall Stats</a></li>
					<li><a href="about"><img src="../images/user-4.png"> About</a></li>
					<li><a href="auth/logout"><img src="../images/out.png"> Logout</a></li>
				</ul>
			</div>
		</div>
