<!DOCTYPE html>
<html>
<!--contact page-->
<head>
	<meta name ="viewport" content ="width=device-width, initial-scale=1">
	<title>Seattle Band Map</title>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
	<link rel="stylesheet" href="page_css.css" />
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/latest/jquery.mobile.min.js"></script>
	<script src="bandmapjs.js"></script>
	
</head>

<body>
<!--page contactPage -->

	<div data-role="page" data-position="fixed" id="contactPage" data-title="Seattle Band Map" data-add-back-btn="true">

	<!--header-->	
	<div data-role="header" class="center-text">
	<p>Contact Us</p>
	</div>
	<!--/header-->
	<!--content of page-->
		<div data-role="content" class="center-text">
			<p>
				If you feel there should be a correction to the band map, please contact us at the following email address:
			</p>
			<p>
				<a href=mailto:seattlebandmap@gmail.com?Subject="Comments" data-role="button" data-theme="a" data-icon="plus" data-inline="true">seattlebandmap@gmail.com</a>
			</p>
			<p>
				Follow Us On Twitter
			</p>
			<p>
				<a href="https://twitter.com/seattlebandmap" data-role="button" data-icon="plus" data-theme="a" data-size="large" data-inline="true">Follow @seattlebandmap</a>
			</p>
		</div>	

	<!--/content-->

	<!--footer-->
		<div data-position="fixed" data-role="footer" class="ui-bar ui-state-persist left-text" data-position="fixed">
			<a href="index.php" data-ajax="false" data-icon="home">Home</a>
			<a href="contact.php">Contact Us</a>
		</div>
	<!--/footer-->

	</div>
<!--/page-->
</body>
</html>
