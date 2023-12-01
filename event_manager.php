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
	<title>Event Manager</title>
	<style>
        .container {
            display: flex;
            justify-content: space-between;
        }

        .column {
            width: 48%;
        }
    </style>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="style.css">
        <script src="https://kit.fontawesome.com/dee0481b32.js" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-ezrDPeQlT1wKKmdotFvV5CjQjGA32KQgq5SHr1wNvUPVP3W6I3MuT2v2pa1hoLIz" crossorigin="anonymous">
        <style>
            <?php include 'style.css'; ?>
        </style>
	
</head>

<body>
	<?php
			require __DIR__ . '/functions.php';
	?>
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
        <h1>Event Manager</h1>


	<h2>Reset All Tables</h2>
	<p>If this is the first time you're running this page, you MUST use reset.</p>

	<form method="POST" action="event_manager.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />

	<div class="container">
        <div class="column">
            <h2>All Events</h2>
            <?php if (connectToDB()) {
                handleDisplayEventsRequest();
            } ?>
            <hr />
            <h2>All Venues</h2>
            <?php if (connectToDB()) {
                handleDisplayVenuesRequest();
            } ?>
        </div>

        <div class="column">
            <h2>Delete Event</h2>
            <form method="POST" action="event_manager.php">
                <input type="hidden" id="deleteEventRequest" name="deleteEventRequest">
                Event ID: <input type="number" name="deleteEventID"> <br /><br />

                <input type="submit" value="Delete" name="deleteSubmit"></p>
            </form>
            <hr />
            <h2>Update Event</h2>
            <form method="POST" action="event_manager.php">
                <input type="hidden" id="updateEventRequest" name="updateEventRequest">
                Event ID: <input type="number" name="updateEventID"> <br /><br />
                New Event Name: <input type="text" pattern="[a-zA-Z0-9 ]+" name="newEventName"> <br /><br />
                New Venue ID: <input type="number" name="newVenueID"> <br /><br />
                New Date Time: <input type="datetime-local" name="newDateTime"> <br /><br />

                <input type="submit" value="Update" name="updateSubmit"></p>
            </form>
        </div>
    </div>

	<hr />

	<div class="container">
        <div class="column">
			<h2>All Sponsors</h2>
            <?php if (connectToDB()) {
                handleDisplaySponsorsRequest();
            } ?>
        </div>
		
        <div class="column">
			<h2>Add New Sponsor</h2>
			<form method="POST" action="event_manager.php">
				<input type="hidden" id="addSponsorRequest" name="addSponsorRequest">
				Sponsor ID: <input type="number" name="addSponsID"> <br /><br />
				Sponsor Name: <input type="text" pattern="[a-zA-Z0-9 ]+" name="addSponsName"> <br /><br />
				Sponsor Address: <input type="text" pattern="[a-zA-Z0-9 ]+" name="addSponsAddress"> <br /><br />
				Sponsor Phone: <input type="text" pattern="\d{3}-\d{3}-\d{4}" name="addSponsPhone"> <br /><br />
				Sponsor Email: <input type="email" name="addSponsEmail"> <br /><br />
				Event ID: <input type="number" name="addSponsEventID"> <br /><br />
				Fund: <input type="number" name="addSponsFund"> <br /><br />
				<input type="submit" value="Add" name="addSubmit">
			</form>
        </div>
    </div>

	<hr />

	<div class="container">
        <div class="column">
			<h2>All Sponsorships</h2>
            <?php if (connectToDB()) {
                handleDisplaySponsorshipsRequest();
            } ?>
        </div>
		
        <div class="column">
			<h2>Add New Sponsorship</h2>
			<form method="POST" action="event_manager.php">
				<input type="hidden" id="addSponsorshipRequest" name="addSponsorshipRequest">
				Sponsor ID: <input type="number" name="addSponsorshipID"> <br /><br />
				Event ID: <input type="number" name="addSponsorshipEventID"> <br /><br />
				Fund: <input type="number" name="addSponsorshipFund"> <br /><br />
				<input type="submit" value="Add" name="addSubmit">
			</form>
        </div>
    </div>

	<hr />

	<h2>Total Sponsors By Event</h2>
	<form method="GET" action="event_manager.php">
		<input type="hidden" id="aggregationRequest" name="aggregationRequest">
		Total number of sponsors of events:<br>
		<?php
		if (connectToDB()) {
			$result = executePlainSQL("SELECT EVENTID, EVENTNAME FROM event");
			while ($row = oci_fetch_array($result, OCI_ASSOC)) {
				$eventID = $row["EVENTID"];
				$eventName = $row["EVENTNAME"];
				echo "<input type='checkbox' name='selectedEventIDs[]' value='$eventID'> Event # $eventID: $eventName<br>";
			}
		}
		?>
		<input type="submit" value="Show" name="aggregation"></p>
	</form>

	<hr />

	<h2>Total Funds By Event</h2>
	<form method="GET" action="event_manager.php">
		<input type="hidden" id="havingAggregationRequest" name="havingAggregationRequest">
		All events whose fund is 
		<select name="comparator">
			<option value="<">less than</option>
			<option value=">">greater than</option>
			<option value="<=">at most</option>
			<option value=">=">at least</option>
		</select>
		<input type="number" name="fund">.
		<input type="submit" value="Show" name="havingAggregation"></p>
	</form>

	<hr />


	<?php

	function printEventsResult($result)
	{
		echo "<table>";
		echo "<tr><th>Event ID</th><th>Venue ID</th><th>Date Time</th><th>Event Name</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["EVENTID"] . "</td><td>" . $row["VENUEID"] . "</td><td>" . $row["DATETIME"] . "</td><td>" . $row["EVENTNAME"] . "</td></tr>";
		}

		echo "</table>";
	}

	function printVenuesResult($result)
	{
		echo "<table>";
		echo "<tr><th>Venue ID</th><th>Venue Name</th><th>Venue Address</th><th>Capacity</th><th>Venue Type</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["VENUEID"] . "</td><td>" . $row["VENUENAME"] . "</td><td>" . $row["VENUEADDRESS"] . "</td><td>" . $row["CAPACITY"] . "</td><td>" . $row["VENUETYPE"] . "</td></tr>";
		}

		echo "</table>";
	}

	function printSponsorsResult($result)
	{
		echo "<table>";
		echo "<tr><th>Sponsor ID</th><th>Sponsor Name</th><th>Sponsor Address</th><th>Sponsor Phone</th><th>Sponsor Email</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["SPONSID"] . "</td><td>" . $row["SPONSNAME"] . "</td><td>" . $row["SPONSADDRESS"] . "</td><td>" . $row["SPONSPHONE"] . "</td><td>" . $row["SPONSEMAIL"] . "</td></tr>";
		}

		echo "</table>";
	}

	function printSponsorshipsResult($result)
	{
		echo "<table>";
		echo "<tr><th>Event ID</th><th>Sponsor ID</th><th>Fund</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["EVENTID"] . "</td><td>" . $row["SPONSID"] . "</td><td>" . $row["FUND"] . "</td></tr>";
		}

		echo "</table>";
	}

	function printaggregationResult($result)
	{
		echo "<table>";
        echo "<tr><th>Event ID</th><th>Event Name</th><th>Total Sponsors</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["EVENTID"] . "</td><td>" . $row["EVENTNAME"] . "</td><td>" . $row["TOTAL_SPONSORS"] . "</td></tr>";
        }
        
		echo "</table>";
	}

	function printHavingAggregationResult($result)
	{
		echo "<table>";
        echo "<tr><th>Event ID</th><th>Event Name</th><th>Total Fund</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
            echo "<tr><td>" . $row["EVENTID"] . "</td><td>" . $row["EVENTNAME"] . "</td><td>" . $row["TOTAL_FUND"] . "</td></tr>";
        }
        
		echo "</table>";
	}


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

	function handleDeleteEventRequest()
	{
		global $db_conn, $success;

		$eventID = $_POST['deleteEventID'];

		$checkID = executePlainSQL("SELECT * FROM event WHERE eventID = $eventID");

		if (oci_fetch_array($checkID, OCI_ASSOC)) {
			executePlainSQL("DELETE FROM event WHERE eventID = $eventID");
			oci_commit($db_conn);
			if ($success) {
				echo "Event successfully deleted. Reload page to update.";
			}
		} else {
			echo "Event not found. Please try again";
		}
	}

	function handleUpdateEventRequest()
	{
		global $db_conn, $success;

		$eventID = $_POST['updateEventID'];
		$venueID = $_POST['newVenueID'];
		$datetime = $_POST['newDateTime'];
		$eventName = $_POST['newEventName'];

		$formattedDatetime = date("Y-m-d H:i:s", strtotime($datetime));

		$checkEventID = executePlainSQL("SELECT * FROM event WHERE eventID = $eventID");
		if (!oci_fetch_array($checkEventID, OCI_ASSOC)) {
			echo "Event not found. Please try again.";
			return;
		}

		$checkVenueID = executePlainSQL("SELECT * FROM venue WHERE venueID = $venueID");
		if (!oci_fetch_array($checkVenueID, OCI_ASSOC)) {
			echo "Venue not found. Please try again.";
			return;
		}

		executePlainSQL("UPDATE event SET venueid = $venueID, datetime=timestamp'" . $formattedDatetime . "',  eventname='" . $eventName . "'WHERE eventid = $eventID");
		oci_commit($db_conn);
		if ($success) {
			echo "Event successfully updated. Reload page to update.";
		}
	}

	function handleAddSponsorRequest()
	{
		global $db_conn, $success;

		$sponsID = $_POST['addSponsID'];
		$sponsName = $_POST['addSponsName'];
		$sponsAddress = $_POST['addSponsAddress'];
		$sponsPhone = $_POST['addSponsPhone'];
		$sponsEmail = $_POST['addSponsEmail'];
		$eventID = $_POST['addSponsEventID'];
		$fund = $_POST['addSponsFund'];

		$sqlSponsor = "INSERT INTO sponsor (sponsid, sponsname, sponsaddress, sponsphone, sponsemail)
				VALUES ($sponsID, '$sponsName', '$sponsAddress', '$sponsPhone', '$sponsEmail')";
		executePlainSQL($sqlSponsor);

		$sqlSponsorship = "INSERT INTO sponsorship VALUES ($eventID, $sponsID, $fund)";
		executePlainSQL($sqlSponsorship);

		oci_commit($db_conn);
		if ($success) {
			echo "Sponsor successfully updated. Reload page to update.";
		}
	}

	function handleAddSponsorshipRequest()
	{
		global $db_conn, $success;

		$sponsID = $_POST['addSponsorshipID'];
		$eventID = $_POST['addSponsorshipEventID'];
		$fund = $_POST['addSponsorshipFund'];

		executePlainSQL("INSERT INTO sponsorship VALUES ($eventID, $sponsID, $fund)");
		oci_commit($db_conn);
		if ($success) {
			echo "Sponsorship successfully updated. Reload page to update.";
		}
	}

	function handleAggregationRequest()
	{
		global $db_conn, $success;

		$selectedEventIDs = $_GET['selectedEventIDs'];

        $result = executePlainSQL(
			"SELECT e.EVENTID
				, e.EVENTNAME
				, COUNT(s.SPONSID) AS TOTAL_SPONSORS
            FROM event e
            LEFT JOIN sponsorship s ON e.EVENTID = s.EVENTID
            WHERE e.EVENTID IN (" . implode(",", $selectedEventIDs) . ")
            GROUP BY e.EVENTID, e.EVENTNAME"
		);

		printaggregationResult($result);
	}

	function handleHavingAggregationRequest()
	{
		global $db_conn;

		$comparator = $_GET['comparator'];
		$fund = $_GET['fund'];
        $result = executePlainSQL(
			"SELECT e.EVENTID
				, e.EVENTNAME
				, SUM(s.FUND) AS TOTAL_FUND
			FROM event e, sponsorship s
			WHERE e.EVENTID = s.EVENTID
			GROUP BY e.EVENTID, e.EVENTNAME
			HAVING SUM(s.FUND) $comparator $fund"
		);

		printHavingAggregationResult($result);
	}

	function handleDisplayEventsRequest()
	{
		global $db_conn;
		$result = executePlainSQL(
			"SELECT EVENTID
				, VENUEID
				, TO_CHAR(DATETIME, 'YYYY-MM-DD HH24:MI:SS') AS DATETIME
				, EVENTNAME
			FROM event"
		);
		printEventsResult($result);
	}

	function handleDisplayVenuesRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM venue");
		printVenuesResult($result);
	}

	function handleDisplaySponsorsRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM sponsor");
		printSponsorsResult($result);
	}

	function handleDisplaySponsorshipsRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT * FROM sponsorship");
		printSponsorshipsResult($result);
	}

	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('updateEventRequest', $_POST)) {
				handleUpdateEventRequest();
			} else if (array_key_exists('addSponsorshipRequest', $_POST)) {
				handleAddSponsorshipRequest();
			} else if (array_key_exists('deleteEventRequest', $_POST)) {
				handleDeleteEventRequest();
			} else if(array_key_exists('addSponsorRequest', $_POST)){
				handleAddSponsorRequest();
			}

			disconnectFromDB();
		}
	}

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('aggregation', $_GET)) {
				handleAggregationRequest();
			} else if (array_key_exists('havingAggregation', $_GET)) {
				handleHavingAggregationRequest();
			} else if (array_key_exists('displayEvents', $_GET)) {
				handleDisplayEventsRequest();
			} else if (array_key_exists('displayVenues', $_GET)) {
				handleDisplayVenuesRequest();
			} else if (array_key_exists('displaySponsors', $_GET)) {
				handleDisplaySponsorsRequest();
			} else if (array_key_exists('displaySponsorships', $_GET)) {
				handleDisplaySponsorshipsRequest();
			}
			
			disconnectFromDB();
		}
	}

	if (isset($_POST['addSubmit']) || isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['deleteSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['aggregationRequest']) || isset($_GET['displayTuplesRequest']) || isset($_GET['havingAggregationRequest'])) {
		handleGETRequest();
	}

	?>
</body>

</html>