<!DOCTYPE html>
<html>
<head>
	<meta name ="viewport" content ="width=device-width, initial-scale=1">
	<title><?php echo $_GET['term']; ?></title>
	<link rel="stylesheet" href="http://code.jquery.com/mobile/latest/jquery.mobile.min.css" />
	<link rel="stylesheet" href="page_css.css" />


		<link href="css/style.css" rel="stylesheet" />

	<script src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script src="http://code.jquery.com/mobile/latest/jquery.mobile.min.js"></script>
	<script src="cytoscape.js/build/cytoscape.js"></script>
	<script src="mapMakerPearlJam.js"></script>
</head>

<body>
<!--page contactPage -->

	<div data-role="page" data-position="fixed" id="search_page" data-title="Seattle Band Map" data-add-back-btn="true">

	<!--header-->	
	<div data-role="header" class="center-text">
	<p>
		<!-- Seattle Band Map -->
		Search Results for "<?php echo $_GET['term']; ?>"
	</p>
</div>
	<!--/header-->
	<!--content of page-->
		<div data-role="content" class="left-text">
			<p>
				<?php
     
		$con = mysql_connect("localhost","webappteam");
		if (!$con) {
			die('Could not connect: ' . mysql_error());
		}
		
		mysql_select_db("bandsdb", $con);
		
		$sql = "SELECT * FROM bands WHERE name LIKE '%" . mysql_escape_string($_GET['term']) . "%'";
		$result = mysql_query($sql,$con);
		
		//$row = mysql_fetch_array($result);
		//echo $row['id'] . '<br/>';
		//echo mysql_num_rows($result) . '<br/>';
		
		if (mysql_num_rows($result) <= 0) {
			echo 'ERROR: No results to display';
		} else if (mysql_num_rows($result) == 1 || $_GET['term'] == "nirvana") {
			echo '<script type="text/javascript" language="javascript">';
            echo 'var connections = new Array();';
            echo 'var i = 0;';
            echo '</script>';
			$row = mysql_fetch_array($result);
			echo '<table width="80%">';
			echo "<tr>";
			echo '<td width="20%">Name: </td>';
			echo "<td>" . $row['name'] . "</td>";
			echo '<script type="text/javascript" language="javascript">';
			echo 'var original_name = "' . $row['name'] . '";';
			echo '</script>';
			echo "</tr>";
			echo '</table>';
			echo '<p>';
/* 			echo 'Connections: '; */
			
			
			$sql_sub = "SELECT * FROM connections WHERE band1=" . $row['id'] . " OR band2=" . $row['id'];
			$result_sub = mysql_query($sql_sub);
			$num_of_connections = mysql_num_rows($result_sub);
			$total_connections = $num_of_connections;
			echo $num_of_connections . ' connections: ';
			//echo $num_of_connections;
			$connection_number = 1;
			while ($row_sub = mysql_fetch_array($result_sub)) {
				$num_of_connections = $num_of_connections - 1;
				if ($row['id'] == $row_sub['band1']) {
					$sql_trip = "SELECT * FROM bands WHERE id=" . $row_sub['band2'];
				} else {
					$sql_trip = "SELECT * FROM bands WHERE id=" . $row_sub['band1'];
				}
				$result_trip = mysql_query($sql_trip);
				$row_trip = mysql_fetch_array($result_trip);
				echo '<b>' . $connection_number . ' </b>';
				echo '<a data-ajax="false" href="search.php?term=' . str_replace(' ', '+', $row_trip['name']) . '">' . $row_trip['name'] . '</a>';
				echo '<script type="text/javascript" language="javascript">';
				echo 'var connections[i] = "' . $row_trip['name'] . '";';
				echo 'i++;';
				echo '</script>';
				if ($num_of_connections > 0) {
					echo ', ';
				}
				$connection_number++;
				//echo '<b>' . $num_of_connections . '</b>';
			}
			
			
			echo '</p>';
			echo '<table width="80%"><tr><td><br/></td><td><br/></td></tr>';
			echo '<tr>';
			echo '<td width="20%">Location: </td>';
			echo '<td>';
			if ($row['city'] <> '' && $row['state']) {
            	echo $row['city'] . ", " . $row['state'];
            } else if ($row['city'] <> '') {
            	echo $row['city'];
            } else if ($row['state'] <> '') {
            	echo $row['state'];
            }
            echo '</td>';
            echo '</tr>';
            echo "<tr>";
            echo "<td>" . "Website: " . "</td>";
            echo "<td>";
            if ($row['website'] <> '' && $row['website'] <> 'http://n/a' && $row['website'] <> 'http://N/A') {
            	echo "<a href=\"" . $row['website'] . "\">" . substr($row['website'], 7) . "</a>";
            }
            echo "</td>";
            echo "</tr>";
            echo "<tr>";
			echo "<td align=left valign=top>" . "Members: " . "</td>";
			if ($row['members'] <> '') {
            	$memberlist = array();
            	$memberlist = explode(',', $row['members']);
            	echo "<td>";
            	foreach ($memberlist as $member) {
	            	echo $member;
	            	echo "<br/>";
				}
				echo "</td>";
			//} else if (mysql_num_rows($result) >= 2) {
			} else {
			echo 'Multiple results available:';
			while ($row = mysql_fetch_array($result)) {
				echo '<br/>';
				echo '<a data-ajax="false" href="search.php?term=' . str_replace(' ', '+', $row['name']) . '">' . $row['name'] . '</a>';
			}
		}
			echo "</tr>";
                
            echo '</table>';
            
            /*
if ($divloader <> "") {
	            echo '<div id="' . $divloader . '"></div>';
            }
*/
            
            //echo '<script type="text/javascript" language="javascript">';
            //echo 'var connections = new Array()';
            //echo 'for (var i = 0; i < ' . mysql_num_rows($result_trip)
		} 
		
		// begin canvas
		echo '<canvas id="map" width="300" height="500" style="border:0px solid #000000;">Your browser does not support HTML5 canvas</canvas>';
		
		echo '<script>';
		echo 'var c=document.getElementById("map");';
		echo 'var ctx=c.getContext("2d");';
		echo 'ctx.beginPath();';
		echo 'ctx.arc(50,250,9,0,2*Math.PI);';
		echo 'ctx.stroke();';
		echo 'ctx.closePath();';
		echo 'ctx.font="12px Arial";';
		echo 'ctx.fillText("0",47,254);';
		$location = 0;
		for ($i = 1; $i <= $total_connections; $i++) {
			$location = 15 + (478 / $total_connections)*($i - 1);
			if (($i % 3) == 0) {
				echo "ctx.strokeStyle='#6f0000';";
			} else if (($i % 3) == 1) {
				echo "ctx.strokeStyle='#006f00';";
			} else {
				echo "ctx.strokeStyle='#00006f';";
			}
			echo 'ctx.beginPath();';
			echo 'ctx.moveTo(59,250);';
			echo 'ctx.lineTo(241,' . $location . ');';
			echo 'ctx.stroke();';
			echo 'ctx.closePath();';
			echo "ctx.strokeStyle='#000000';";
			echo 'ctx.beginPath();';
			echo 'ctx.arc(250,' . $location . ',9,Math.PI,3*Math.PI);';
			echo 'ctx.stroke();';
			echo 'ctx.closePath();';
			echo 'ctx.strokeStyle="black";';
			echo 'ctx.font="12px Arial";';
			if ($i < 10) {
				echo 'ctx.fillText("' . $i . '",247,' . ($location + 4) . ');';
			} else {
				echo 'ctx.fillText("' . $i . '",243,' . ($location + 4) . ');';
			}
		}
		echo 'ctx.stroke();';
		
		echo '</script>';
		
		echo '</p>';
		echo '</div>';
		
		?>

			<!--
</p>
		</div>
-->
	<!--/content-->

		<!--
<?php
		
			if (!($divloader == "")) {
	            echo '<div id="' . $divloader . '"></div>';
	            echo '<p>Foo</p>';
            }
		
		?>
-->

	<!-- <div id="demo"></div> -->

	<!--footer-->
		<div data-role="footer" data-position="fixed"  class="ui-bar ui-state-persist left-text" data-position="fixed">
			<a href="index.php" data-ajax="false" data-icon="home">Home</a>
			<a href="contact.php">Contact Us</a>
		</div>
	<!--/footer-->

	</div>
<!--/page-->
</body>
</html>
