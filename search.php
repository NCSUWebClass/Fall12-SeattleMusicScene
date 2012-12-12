<head>
	<title>Seattle Band Map</title>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
	<link rel="stylesheet" type="text/css" href="page_css.css" />
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/latest/jquery.mobile.min.js"></script>
	<?
		$mysql_server = "localhost";// Ryan Fredette: these are the stats for my machine,
		$mysql_user = "bandmap";// and they'll need to be changed for use on another server
		$mysql_pass = "bandmap";
		$mysql_db_name = "seattlebandmap";
		// the code that executes the search, or returns the user to the index if no search term is given
		if (!empty($_GET["id"])) {
			$id = $_GET["id"];
			mysql_connect($mysql_server, $mysql_user, $mysql_pass);
			mysql_select_db($mysql_db_name);
			$query = "SELECT * FROM bands WHERE `id`='" . (string)$id . "';";
			$result = mysql_query($query);
			$num = mysql_num_rows($result);
			mysql_close();
		} else if (!empty($_GET["query"])){
			$name = $_GET["query"];
			mysql_connect($mysql_server, $mysql_user, $mysql_pass);
			mysql_select_db("seattlebandmap");
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
					//Name
					echo(mysql_result($result, 0, "name") . "<br/>");
					//connections
					mysql_connect($mysql_server, $mysql_user, $mysql_pass);
					mysql_select_db($mysql_db_name);
					$id = mysql_result($result, 0, "id");
					$query = "SELECT * FROM connections WHERE `band1`=" . (string)$id . " OR `band2`=" . (string)$id . ";";
					$connections = mysql_query($query);
					$num = mysql_num_rows($connections);
					echo("<div class=\"connections\" data-role=\"content\">\n");
					if ($num == 1) {//any number other than one should say "connections", whereas if there is only 1 connection, it should not be plural
						echo("1 Connection: ");
					} else {
						echo((string)$num . " Connections: ");
					}
					for( $i = 0; $i < $num; $i++) {
						$connected_id = mysql_result($connections, $i, "band1");
						if (strcmp($connected_id, $id) == 0) {//if the id is the same as the band we're generating a graph from
							$connected_id = mysql_result($connections, $i, "band2");//use the other id that is provided
						}
						$query = "SELECT * FROM bands WHERE `id`=" . (string)$connected_id . ";";
						$connected_band = mysql_query($query);
						if (mysql_num_rows($connected_band) != 1) {
							throw new Exception("There are " . mysql_num_rows($connected_band) . " bands with id " . $connected_id);
						}
						if ($i != 0) {
							echo(", "); //put a comma before every item other than the first in the comma seperated list
						}
						echo("<a href=search.php?id=" . $connected_id . ">");
						echo(filter_var(mysql_result($connected_band, 0, "name"), FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
						echo("</a>");
					}
					echo("\n</div>");
					//location
					echo("<div class=\"location\" data-role=\"content\">\n");
					echo("Location: ");
					$city = mysql_result($result, 0, "city");
					$state = mysql_result($result, 0, "state");
					$location .= $city . ", " . $state;
					$location = trim($location, ", ");
					echo(filter_var($location, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
					echo("\n</div>");
					//website
					echo("<div class=\"website\" data-role=\"content\">\n");
					$website = mysql_result($result, 0, "website");
					echo("Website: ");
					echo("<a href=" . $website . ">");
					echo(filter_var($website, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
					echo("</a>\n</div>");
					//members
					echo("<div class=\"website\" data-role=\"content\">\n");
					echo("Members: " . filter_var(mysql_result($result, 0, "members"), FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
					echo("\n</div>");
					//edit button?
					mysql_close();
				} else {
					echo("There are " . (string)$num . " results. Did you mean:<br/>");
					for( $i = 0; $i < $num; $i++) {
						$name = mysql_result($result,$i,"name");
						$id = mysql_result($result,$i,"id");
						echo("<a href=search.php?id=" . (string)$id . ">" . $name . "</a><br/>");
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
