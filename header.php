<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="author" content="F. Baumann, H. Winter" />
<meta name="description" content="CoffeeStats | All about coffee" />
<title>CoffeeStats</title>
<link rel="stylesheet" type="text/css" href="../css/caffeine.css" />
</head>
<body>

<div id="wrapper">

	<div id="header">
        <h1>coffeestats.org</h1>
        	<p>You drink it, we count!</p>
	</div>
	
	<div id="sidebar">
		<div class="section">
        <p>Welcome <strong><?php echo $_SESSION['login_user']; ?></strong>.</p>
				<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="plusone.php">I made Coffee!</a></li>
                <li><a href="profile.php?u=<?php echo $_SESSION['login_user']; ?>">Statistic</a></li>
				<li><a href="overallstatistic.php">Overall Statistic</a></li>
				<li><a href="settings.php">Settings</a></li>
				<li><a href="about.php">About</a></li>
				<li><a href="auth/logout.php">Logout</a></li>
				</ul>
		</div>
		
		<div class="section">
            <p class="center">coffeestats.org is a project, written with &hearts; by Florian Baumann and Holger Winter. Get in touch <a href="about.php">&rarr;</a></p>
		</div>
	</div>
	
	<div id="content"> <!-- end of header.php -->
