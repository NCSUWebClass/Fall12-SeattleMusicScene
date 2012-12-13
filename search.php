<head>
	<title>Seattle Band Map</title>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
	<link rel="stylesheet" type="text/css" href="page_css.css" />
	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/latest/jquery.mobile.min.js"></script>
	<?
		$mysql_server = "localhost";// Ryan Fredette: these are the stats for my machine,
		$mysql_user = "webappteam";//server side
		$mysql_db_name = "bandsdb";
		$mysql_conn = mysqli_connect($mysql_server, $mysql_user);
		$mysql_conn->select_db($mysql_db_name);

		$search_stmt = null;
		$id = null;
		$name = null;
		$city = null;
		$state = null;
		$website = null;
		$members = null;
		$num_rows = null;

		// the code that executes the search, or returns the user to the index if no search term is given
		if (!empty($_GET["id"])) {
			$id = $_GET["id"];
			$search_stmt = $mysql_conn->prepare("SELECT id, name, city, state, website, members FROM bands WHERE `id`=?;");
			echo($mysql_conn->error);
			$search_stmt->bind_param('i', $id);
			$search_stmt->execute();
			$search_stmt->bind_result($id, $name, $city, $state, $website, $members);
			$search_stmt->store_result();
			$num_rows = $search_stmt->num_rows;
		} else if (!empty($_GET["query"])){
			$name = $_GET["query"];
			$search_stmt = $mysql_conn->prepare("SELECT id, name, city, state, website, members FROM bands WHERE `name` LIKE CONCAT ('%', ? , '%');");
			$search_stmt->bind_param('s', $name);
			$search_stmt->execute();
			$search_stmt->bind_result($id, $name, $city, $state, $website, $members);
			$search_stmt->store_result();
			$num_rows = $search_stmt->num_rows;
		} else {
			$mysql_conn->close();
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
				if ($num_rows == 0) {
					echo("No bands match the name you input.");
				} else if ($num_rows == 1) {
					/* graph code also goes here */
					$search_stmt->fetch();
					//Name
					echo($name . "<br/>");
					//connections
					$band1 = null;
					$band2 = null;
					$connected_bands_stmt = $mysql_conn->prepare("SELECT band1, band2 FROM connections WHERE `band1`=? OR `band2`=?;");
					$connected_bands_stmt->bind_param('ii', $id, $id);
					$connected_bands_stmt->execute();
					$connected_bands_stmt->bind_result($band1, $band2);
					$connected_bands_stmt->store_result();
					$num_rows = $connected_bands_stmt->num_rows;
					echo("<div class=\"connections\" data-role=\"content\">\n");
					if ($num_rows == 1) {//any number other than one should say "connections", whereas if there is only 1 connection, it should not be plural
						echo("1 Connection: ");
					} else {
						echo((string)$num_rows . " Connections: ");
					}
					$connected_band_names = "";
					while ($connected_bands_stmt->fetch()) {
						$connected_id = $band1;
						if (strcmp($connected_id, $id) == 0) {//if the id is the same as the band we're generating a graph from
							$connected_id = $band2;//use the other id that is provided
						}
						$connected_stmt = $mysql_conn->prepare("SELECT name FROM bands WHERE `id`=?;");
						echo($mysql_conn->error);
						$connected_stmt->bind_param('i', $connected_id);
						$connected_stmt->execute();
						$connected_stmt->bind_result($connected_band);
						$connected_stmt->store_result();
						if ($connected_stmt->num_rows != 1) {
							throw new Exception("There are " . $connected_stmt->num_rows . " bands with id " . $connected_id);
						}
						$connected_stmt->fetch();
						$connected_band_names .= "<a href=search.php?id=" . $connected_id . ">";
						$connected_band_names .= filter_var($connected_band, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
						$connected_band_names .= "</a>, ";
					}
					echo(trim($connected_band_names, ", "));
					echo("\n</div>");
					//location
					echo("<div class=\"location\" data-role=\"content\">\n");
					echo("Location: ");
					$location .= $city . ", " . $state;
					$location = trim($location, ", ");
					echo(filter_var($location, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
					echo("\n</div>");
					//website
					echo("<div class=\"website\" data-role=\"content\">\n");
					echo("Website: ");
					echo("<a href=" . $website . ">");
					echo(filter_var($website, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
					echo("</a>\n</div>");
					//members
					echo("<div class=\"website\" data-role=\"content\">\n");
					echo("Members: " . filter_var($members, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
					echo("\n</div>");
					//edit button?
				} else {
					echo("There are " . (string)$num_rows . " results. Did you mean:<br/>");
					while ( $search_stmt->fetch() ) {
						echo("<a href=search.php?id=" . (string)$id . ">" . $name . "</a><br/>");
					}
				}
				$mysql_conn->close();
			?>
		</p>
	</div>
	<!--/main content-->
	<!--footer-->
		<div data-role="footer"  class="ui-bar ui-state-persist left-text"  data-position="fixed"><a href="index.php" data-ajax="false" data-icon="home">Home</a><a href="contact.php" >Contact Us</a></div>
	<!--/footer-->
</body>
