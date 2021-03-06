<?php
$account_combined = false;
$max_combined = 0;
$account_community = false;
$max_community = $total_community = 0;
$account_tutoring = false;
$max_tutoring = $total_tutoring = 0;
$events = 10;
$mention = "";
require_once('dbconfig.php');
try {
	$dbh = new PDO($driver, $user, $pass, $attr);
	try {
		$stmt = $dbh->prepare('SELECT `studentname`, `community`, `tutoring` FROM `members`');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			if ($row['tutoring'] + $row['community'] > $max_combined) {
				$account_combined = $row;
				$max_combined = $row['tutoring'] + $row['community'];
			}
			if ($row['community'] > $max_community) {
				$account_community = $row;
				$max_community = $row['community'];
			}
			if ($row['tutoring'] > $max_tutoring) {
				$account_tutoring = $row;
				$max_tutoring = $row['tutoring'];
			}
			$total_community += $row['community'];
			$total_tutoring += $row['tutoring'];
		}
		$stmt = $dbh->prepare('SELECT `id` FROM `events`');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$events++;
		}
		$stmt = $dbh->prepare('SELECT `value` FROM `vars` WHERE `key` = "mention"');
		$stmt->execute();
		while ($row = $stmt->fetch()) {
			$mention = $row['value'];
			break;
		}
		unset($stmt);
		unset($dbh);
	} catch (Exception $f) {
		echo "Exception: " . $f->getMessage() . "<br />";
	}
} catch (Exception $e) {
	$recipient = "errors@bownhs.org";
	$subject = "SQL Connection";
	$mail_body = "An exception occurred on the homepage: " . $e->getMessage();
	mail($recipient, $subject, $mail_body);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="A platform for communication and organization of the Bow High School's chapter of the National Honor Society">
	<meta name="author" content="Dylan Wheeler">

	<title>National Honor Society</title>

	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/logo144.png">
	<link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/logo114.png">
	<link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/logo72.png">
	<link rel="apple-touch-icon-precomposed" sizes="57x57" href="images/logo57.png">
	<link rel="shortcut icon" href="images/favicon.png">

	<!-- Bootstrap Core CSS -->
	<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="css/business-frontpage.css" rel="stylesheet">

</head>

<body>

	<!-- Navigation -->
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="members/#">Members Portal</a>
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav">
					<li>
						<a href="#about">About</a>
					</li>
					<li>
						<a href="#members">Members</a>
					</li>
					<li>
						<a href="#services">Services</a>
					</li>
				</ul>
			</div>
			<!-- /.navbar-collapse -->
		</div>
		<!-- /.container -->
	</nav>

	<!-- Image Background Page Header -->
	<!-- Note: The background image is set within the business-casual.css file. -->
	<header class="business-header">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">
					<h1 class="tagline">&nbsp;</h1>
				</div>
			</div>
        <img class="img-responsive img-center" src="images/logo.png" alt="">
	</header>

	<!-- Page Content -->
	<div class="container">

		<section id="about" class="container content-section">

			<hr />

			<div class="row">
				<div class="col-sm-8">
					<h2>What We Do</h2>
					<p>The National Honor Society is a group of honors students at Bow High School who have been carefully picked to represent our school. The National Honor Society handles most of the volunteering that the school does for organizations as well as provides free tutoring services to the school. This year, our Chapter has accumulated over <b><?php echo floor($total_community); ?></b> community service hours and <b><?php echo floor($total_tutoring); ?></b> tutoring hours while also hosting over <b><?php echo $events; ?></b> events ranging from community service to fundraising for our school and community. <a href="https://docs.google.com/forms/d/1NaGzCJ5m-7ihMaNPuD6oij5igycsg93JvsMt85j3oug/viewform" target="_blank">Let us know if you need our tutors!</a></p>
				</div>
				<div class="col-sm-4">
					<h2>Advisors</h2>
					<address>
						<strong>William Dodge</strong>
						<br />Integrated Geometry Teacher
						<br />Pre-Calculus and Calculus Teacher
						<br />Math Team Coach
						<br /><a href="mailto:wdodge@bownet.org">wdodge@bownet.org</a>
					</address>
					<address>
						<strong>Lily Woo</strong>
						<br />World Studies Teacher
						<br />AP European History Teacher
						<br /><a href="mailto:lwoo@bownet.org">lwoo@bownet.org</a>
					</address>
				</div>
			</div>
			<!-- /.row -->

			<hr />

			<div class="row">
				<div class="col-sm-3">
					<img class="img-circle img-responsive img-center" src="images/character.png" alt="">
					<h2>Character</h2>
					<p>Character is what distinguishes one individual from another. It is the product of constant striving to make the right choices day after day. Students with good character demonstrate respect, responsibility, trustworthiness, fairness, caring, and citizenship in all of their actions.</p>
				</div>
				<div class="col-sm-3">
					<img class="img-circle img-responsive img-center" src="images/scholarship.png" alt="">
					<h2>Scholarship</h2>
					<p>Scholarship is characterized by a commitment to learning. A student is willing to spend the necessary time to cultivate his/ her mind in the quest for knowledge. This pillar can only be achieved through diligence and effort. Scholarship means always doing the best work possible, regardless of impending reward.</p>
				</div>
				<div class="col-sm-3">
					<img class="img-circle img-responsive img-center" src="images/leadership.png" alt="">
					<h2>Leadership</h2>
					<p>Leaders take the initiative to aid others in a wholesome manner throughout their daily activities. Leaders sacrifice their personal interests in order to yield to the needs of others. Leaders need wisdom and self-confidence to affect change in all aspects of their lives.</p>
				</div>
				<div class="col-sm-3">
					<img class="img-circle img-responsive img-center" src="images/service.png" alt="">
					<h2>Service</h2>
					<p>The pillar of service can be reached in a variety of ways. The willingness to work for the benefit of those in need without compensation or recognition of any kind is a quality that is essential in NHS members. As a service club, the National Honor Society is highly concerned with giving its all to the school and community at large.</p>
				</div>
			</div>
			<!-- /.row -->
		</section>

		<section id="members" class="container content-section">

			<hr />

			<div class="row">

				<div class="col-sm-6">

					<h2>Chapter Members</h2>

					<p>The Bow High School Chapter of the National Honor Society is filled with brilliant young minds looking to make a difference in their school. The school&#39;s members come from the junior and senior classes and represent the best BHS has to offer.</p>
					<p>The senior members this year are: Madison Beauchain, Shannon Benson, Madeleine Cheney, Anthony Dal Pos, Casey Day, Jillian DeLand, John Graham, Margaret Jensen, Brooke Johnson, Hadley Johnson, Robert Joscelyn, Kaitlynn Leary, Samantha MacEachron, Justin McCully, Claire Murray, Ryan Murray, Michelle Neal, Alina Pinney, Aditya Shah, Samrawit Silva, Kaythi Tu, Evan Vulgamore, Katrina Wells, Shane Wunderli, and Laura Zbehlik.</p>
					<p>The inductees this year are: Austin Beaudette, Kristen Benson, Duke Biehl, Sullivan Blair, Anthony Celenza, Lucas Cohen, Mason Elle-Gelernter, Sebastian Grasso, Riley Hicks, Abigail Horner, Paige Johnson, Nandita Kasireddy, Rebecca Katz, Olivia Krause, Conner Lorenz, Hallie Lothrop, Joseph Lulka, Emily Montebianchi, Michael Mullen, Claire Mulvaney, Samuel Neff, Bryce Northrop, Brandon Parker, Elizabeth Pizzi, Isabella Urbina, Jack Vachon, Sadie Warburton, Elin Warwick, Brendan Winch, and Elysia Woody.</p>

				</div>

				<div class="col-sm-6">

					<p>&nbsp;</p>

					<p><strong>President:</strong> Hayden Udelson has been an avid member of Bow High School for the past three years and looks forward to finishing with a very productive year. He runs cross country, skis Nordic, and plays tennis.</p>

					<p><strong>VP:</strong> Sohani Demian is a junior this year and will become President of NHS next year. She is a three sport varsity athlete (cross country, Nordic, and track). She is very excited to be on the executive board and to work with everyone this year!</p>

					<p><strong>Secretaries:</strong> Julia Currier is a three sport varsity athlete playing soccer, unified basketball, and lacrosse. She loves to make sure everyone is happy and is beyond excited to help everyone in NHS and the school this year.<br />Caitlin Keenan is also a secretary for NHS and a senior at Bow. She plays soccer at the high school and on a travel club team. Caitlyn is also secretary for Student Senate and the Student Athlete Leadership Council.</p>

					<p><strong>Treasurer:</strong> Owen Molind is a two sport athlete at Bow High school (soccer and Nordic) and a two-year captain. He also plays soccer in the spring with his club team FC Greater Londonderry.  He is a four-year member of the Student Senate and will serve as its Treasurer this year too.</p>

					<p><strong>Parliamentarian:</strong> Sarah Zecha is a senior at Bow High School. She is a three sport varsity athlete (cross country, Nordic, and tennis) and loves all three. She is excited to be on the executive board of NHS.</p>

					<p><strong>Webmaster:</strong> Dylan Wheeler has been programming for six years, two of which have been for web development. As a senior, he plays football and runs track. He intends to go to college to pursue computer science and business.</p>

					<p><strong>Historian:</strong> Amanda Murray plays soccer, basketball, and lacrosse and can always be bribed with cookie dough. She can&#39;t wait for another great year on the National Honor Society!</p>

				</div>

			</div>
		</section>

		<section id="noteworthy" class="container content-section">

			<hr />

			<div class="row">
				<div class="col-sm-12">
					<h2>Noteworthy Members</h2>
					<div class="row">
						<div class="col-sm-4">
							<p><?php echo '<strong>' . $account_combined['studentname'] . '</strong> has been proven the most dedicated member of the Society by amassing a total of <strong>' . floor($max_combined) . '</strong> hours of tutoring and community service, the highest combined total of any of our members! Congratulations!'; ?></p>
						</div>
						<div class="col-sm-4">
							<p><?php echo '<strong>' . $account_community['studentname'] . '</strong> has given back to the community by volunteering through the Society earning <strong>' . floor($max_community) . '</strong> hours! This is the most community service done by any of our members. Awesome job!'; ?></p>
						</div>
						<div class="col-sm-4">
							<p><?php echo '<strong>' . $account_tutoring['studentname'] . '</strong> has earned the title of Most Tutoring Completed by donating <strong>' . floor($max_tutoring) . '</strong> hours toward benefiting the education of fellow peers! You stand out among fellow Society members for your altruism. Well done!'; ?></p>
						</div>
					</div>
				</div>
				<div class="col-sm-12">
					<h2>Honorable Mention <small>courtesy of the NHS advisors:</small></h2>
					<div class="row">
						<div class="col-sm-12">
							<p>"<?php echo $mention; ?>"</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<section id="services" class="container content-section">

			<hr />

			<div class="row">
				<div class="col-md-6">
					<h2>Volunteering</h2>
					<p>If you have any requests for volunteering hands, email one of the Chapter&#39;s advisors. Contact information is provided <a href="#about">above</a>.</p>
				</div>
				<div class="col-md-6">
					<h2>Tutoring</h2>
					<p>If you or your child needs tutoring, please take a moment to fill out the form below. Your request will be met as soon as we find a member best suited for your needs!</p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6">
					<img class="img-responsive img-center" src="images/logo.png" alt="National Honor Society" />
				</div>
				<div class="col-md-6">
					<iframe src="https://docs.google.com/forms/d/1NaGzCJ5m-7ihMaNPuD6oij5igycsg93JvsMt85j3oug/viewform?embedded=true" width="100%" height="329" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
				</div>
			</div>
		</section>

		<section id="contact" class="container content-section">

			<div class="row">
				<div class="col-lg-12">
					<h2>Contact</h2>
					<p>If there are any problems with this website, you have a suggestion for content, or you wish to contact the Webmaster in general, send an email to <a href="mailto:ask@bownhs.org">ask@bownhs.org</a>. If you are intending to contact our advisors, their contact information is available <a href="#about">above</a>.</p>
					<p>Bow High School resides at 55 Falcon Way in Bow, New Hampshire. The office can be reached at (603) 228-2210 or faxed at (603) 228-2212.</p>
				</div>
			</div>
		</section>
		
		<hr>

		<!-- Footer -->
		<footer>
			<div class="row">
				<div class="col-lg-12">
					<p>Copyright &copy; Bow High School 2015</p>
				</div>
			</div>
			<!-- /.row -->
		</footer>

	</div>
	<!-- /.container -->

	<!-- jQuery -->
	<script src="bower_components/jquery/jquery.min.js"></script>

	<!-- Bootstrap Core JavaScript -->
	<script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

	<script type="text/javascript">
	// Scrolls to the selected menu item on the page
	$(function() {
		$('a[href*=#]:not([href=#])').click(function() {
			if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') || location.hostname == this.hostname) {
				var target = $(this.hash);
				target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
				if (target.length) {
					$('html,body').animate({
						scrollTop: target.offset().top
					}, 1000);
					return false;
				}
			}
		});
	});
	</script>

</body>

</html>