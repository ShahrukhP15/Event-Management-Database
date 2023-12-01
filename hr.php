<?php

// Database access configuration
$config["dbuser"] = "ora_";			// change "cwl" to your own CWL
$config["dbpassword"] = "a";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;
$success = true;
$show_debug_alert_messages = False;

?>
<!DOCTYPE html>
<html>

	<head>
		<title>HR</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="preconnect" href="https://fonts.gstatic.com">
			<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
			<link rel="stylesheet" href="style.css">
			<script src="https://kit.fontawesome.com/dee0481b32.js" crossorigin="anonymous"></script>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-ezrDPeQlT1wKKmdotFvV5CjQjGA32KQgq5SHr1wNvUPVP3W6I3MuT2v2pa1hoLIz" crossorigin="anonymous">
			<style>
				<?php include 'style.css'; ?>
			</style>

	<style>
	.container {
        display: flex;
		justify-content: space-between;
    	}

    .column {
        flex: 50%;
		width: 40%;
		padding: 5px;
    	}
	</style>
		
	</head>

	<body>
		<div class="navbar">
				<a href="index.php"><i class="fas fa-home"></i></a>
				<div class="dropdown">
					<button class="dropbtn" style="margin-right: 100px;">User Login</button>
					<div class="dropdown-content">
						<a href="event_manager.php">Event Manager</a>
						<a href="customerservice.php">Customer Service</a>
						<a href="hr.php">Human Resource</a>
					</div>
				</div>
			</div>
			<h1>HR</h1>

			<?php
			require __DIR__ . '/functions.php';
			?>
			<h2>Reset All Tables</h2>
		<p>If this is the first time you're running this page, you MUST use reset.</p>

		<form method="POST" action="hr.php">
			<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
			<p><input type="submit" value="Reset" name="reset"></p>
		</form>

		<hr />

		<div class="container">
			<div class="column">
				<h2>All Staff Assignments</h2>
				<?php if (connectToDB()) {
					handleDisplayAssignmentRequest();
				} ?>
				<hr />
			</div>

        	<div class="column">
            	<h2>All Events</h2>
            	<?php if (connectToDB()) {
                	handleDisplayEventsRequest();
            	} ?>
            	<hr />
			</div>
		</div>
		

		<div>
		<h2><u>Browse staff by:</u></h2>
		<form method="POST" action="hr.php">
			<input type="hidden" id="projectionRequest" name="projectionRequest">
			<input type='checkbox' name='staff[]' value='staffid'>ID
			<input type="checkbox" name="staff[]" value="staffname">Name<br>
			<input type="checkbox" name="staff[]" value="staffaddress">Address<br>
			<input type="checkbox" name="staff[]" value="staffphone">Phone<br>
			<input type="checkbox" name="staff[]" value="staffemail">Email<br>
			<p><input type="submit" value="Show" name="staffProjectionSubmit"></p>
		</form>
		</div><br>

		<div>
		<h2><u>Find staffs who were assigned in all events:</u></h2>
		<form method="GET" action="hr.php">
				<input type="hidden" id="divisionRequest" name="divisionRequest">
				<p><input type="submit" value="Show" name="divisionSubmit"></p>
			</form>
		</div><br>

		<?php
			function handleResetRequest()
		{	
			global $db_conn;
			$sqlFilePath = './event_management.sql';
			$sqlContent = file_get_contents($sqlFilePath);
			$sqlStatements = explode(';', $sqlContent);
			$sqlStatements = array_filter($sqlStatements, 'trim');
			
			foreach ($sqlStatements as $cmdstr) {
				executePlainSQL($cmdstr);
			}
			echo "<br> All tables reset. Reload page to update.<br>";
			
			oci_commit($db_conn);
		}

		function handleProjectionRequest() {
			global $db_conn;

			echo "<table>";
			echo "<tr>";

			// Get selected staff attributes from the form
			$checkboxes = $_POST['staff'];  
			$selectedAttributes = "";
			$numAttributes = 0;

			foreach ($checkboxes as $selectedAttribute) {
				if (!empty($selectedAttribute)) {
					echo "<th>" . $selectedAttribute . "</th>";
					$selectedAttributes .= $selectedAttribute . ",";
					$numAttributes += 1;
				}
			}

		echo "</tr>"; 
		$selectedAttributes = rtrim($selectedAttributes, ",");

		// Perform projection query to get selected staff attributes
		$result = executePlainSQL("SELECT $selectedAttributes FROM staff");
		
		// Print the result
		printResult($result, $numAttributes);
		}

		function printResult($result, $numAttributes) {
			echo "<br><u><b>Staff Members in System</b></u><br><br>";
			
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr>";  // Start a new HTML table row for each staff member
				for ($colIndex = 0; $colIndex < $numAttributes; $colIndex++) {
					echo "<td>" . $row[$colIndex] . "</td>";  // Output each staff attribute as a table cell
				}
				echo "</tr>";  // End the HTML table row
			}

			echo "</table>";  // End the HTML table
		}

		function handleDivisionRequest() {
			$result = executePlainSQL(
				"SELECT DISTINCT s.staffid, s.staffname
				FROM staff s, assignment a
				WHERE NOT EXISTS (
					SELECT e.eventid
					FROM event e
					WHERE NOT EXISTS (
						SELECT a.eventid
						FROM assignment a
						WHERE a.staffid = s.staffid
						AND a.eventid = e.eventid
				)
			)"
			);

			printDivisionResult($result);
		}

		function printDivisionResult($result){    
			echo "<br><u><b>STAFF MEMBERS WHO HAVE WORKED IN ALL EVENTS</b></u><br><br>";
			echo "<table>";
			echo "<tr><th>Staff ID</th><th>Staff Name</th></tr>";

			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row['STAFFID'] . "</td><td>" . $row['STAFFNAME'] . "</td></tr>";
			}
			echo "</table>";
		}

		function handleDisplayAssignmentRequest(){
			global $db_conn;
			$result = executePlainSQL(
				"SELECT *
				FROM assignment"
			);
			printAssignmentResult($result);
		}

		function handleDisplayEventsRequest(){
			global $db_conn;
			$result = executePlainSQL(
				"SELECT *
				FROM event"
			);
			printEventsResult($result);
		}

		function printAssignmentResult($result)
		{
			echo "<table>";
			echo "<tr><th>Event ID</th><th>Staff ID</th><th>Role </th></tr>";

			while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
				echo "<tr><td>" . $row["EVENTID"] . "</td><td>" . $row["STAFFID"] . "</td><td>" . $row["ROLE"] . "</td></tr>";
			}

			echo "</table>";
		}

		function printEventsResult($result)
		{
			echo "<table>";
			echo "<tr><th>Event ID</th><th>Venue ID</th><th>Date Time</th><th>Event Name</th></tr>";

			while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
				echo "<tr><td>" . $row["EVENTID"] . "</td><td>" . $row["VENUEID"] . "</td><td>" . $row["DATETIME"] . "</td><td>" . $row["EVENTNAME"] . "</td></tr>";
			}

			echo "</table>";
		}

		function handlePOSTRequest()
			{
				if (connectToDB()) {
					if (array_key_exists('resetTablesRequest', $_POST)) {
						handleResetRequest();
					} else if (array_key_exists('projectionRequest', $_POST)) {
						handleProjectionRequest();
					}
					disconnectFromDB();
				}
			}

		function handleGETRequest()
			{
				if(connectToDB()) {
					if(array_key_exists('divisionRequest', $_GET)) {
						handleDivisionRequest();
					}
					else if (array_key_exists('assignment_table', $_GET)) {
						handleDisplayAssignmentRequest();
					}
					elseif (array_key_exists('displayEvents', $_GET)) {
						handleDisplayEventsRequest();
					}
				disconnectFromDB();
				}

			}   


		if (isset($_POST['reset']) || isset($_POST['staffProjectionSubmit'])) {
			handlePOSTRequest();
		} elseif (isset($_GET['divisionSubmit'])) {
			handleGETRequest();
		} 

		?>
</body>

</html>


        


        