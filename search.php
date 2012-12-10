<head>
	<title>Seattle Band Map</title>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
	<link rel="stylesheet" type="text/css" href="page_css.css" />
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/latest/jquery.mobile.min.js"></script>
	<?
		// the code that executes the search, or returns the user to the index if no search term is given
		if (!empty($_GET["id"])) {
			$id = $_GET["id"];
			mysql_connect("localhost", "bandmap", "bandmap");
			mysql_select_db("seattlebandmap");
			$query = "SELECT * FROM bands WHERE `id`='" . (string)$id . "';";
			$result = mysql_query($query);
			$num = mysql_num_rows($result);
			mysql_close();
		} else if (!empty($_GET["query"])){
			$name = $_GET["query"];
			mysql_connect("localhost", "bandmap", "bandmap"); //Ryan Fredette: these are my local credentials for accessing the mysql server.
			mysql_select_db("seattlebandmap"); 				// we need to change these to match the ones on the server
			$query = "SELECT * FROM bands WHERE `name` like '%" . mysql_escape_string($name) . "%';";
			$result = mysql_query($query);
			$num = mysql_num_rows($result);
			mysql_close();
		} else {
			header("Location: index.php");
		}
	?>
</head>
<body>
	<!--header-->
	<div data-position="fixed" data-role="header" class="center-text">
		<p>Seattle Band Map</p>
	</div>
	<!--/header-->
	<!--main content-->
	<div data-role="content">
		<p class="center-text">
			<h1 class="center-text">Welcome to the Seattle Band Map</h1>
			<br/>
			<form action="search.php" method="get" data-ajax="false">
				<input type="text" name="query" id="search-box" placeholder="Artist or Band Name" data-theme="a" data-ajax="false" value="<?= $_GET["query"]?>" />
				<input type="submit" value="Submit" data-theme="a" data-inline="true" data-ajax="false"/>
			</form>
			<br/>
			<br/>
		</p>
		<p class="center-text">
			<?
				if ($num == 0) {
					echo("No bands match the name you input.");
				} else if ($num == 1) {
					/* graph code also goes here */
					echo("You have selected the band " . mysql_result($result, 0, "name") . ".<br/>"); //XXX: remove this line when graph is added
				} else {
					echo("There are " . (string)$num . " results. Did you mean:<br/>");
					$i = 0;
					while( $i < $num) {
						$name = mysql_result($result,$i,"name");
						$id = mysql_result($result,$i,"id");
						echo("<a href=search.php?id=" . (string)$id . ">" . $name . "</a><br/>");
						$i++;
					}
				}
			?>
		</p>
	</div>
	<!--/main content-->
	<!--footer-->
		<div data-role="footer"  class="ui-bar ui-state-persist left-text"  data-position="fixed"><a href="index.php" data-ajax="false" data-icon="home">Home</a><a href="contact.php" >Contact Us</a></div>
	<!--/footer-->
</body>
