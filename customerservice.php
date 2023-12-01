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
		<title>Customer Service</title>
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<link rel="preconnect" href="https://fonts.gstatic.com">
			<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
			<link rel="stylesheet" href="style.css">
			<script src="https://kit.fontawesome.com/dee0481b32.js" crossorigin="anonymous"></script>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" integrity="sha384-ezrDPeQlT1wKKmdotFvV5CjQjGA32KQgq5SHr1wNvUPVP3W6I3MuT2v2pa1hoLIz" crossorigin="anonymous">
			<style>
				<?php include 'style.css'; ?>
			</style>


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
		<h1>Customer Service</h1>

	<h2>Reset All Tables</h2>
	<p>If this is the first time you're running this page, you MUST use reset.</p>

	<form method="POST" action="customerservice.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />

	<h2>All Customers</h2>
	<?php connectToDB(); handleDisplayCustomerRequest(); ?>

	<hr />

	<h2>Customer Tiers</h2>
	<p>Find all customers with given tier.</p>
	<form method="POST" action="customerservice.php">
		<input type="hidden" id="selectTuplesRequest" name="selectTuplesRequest">
		Select Tier:
		<select id="tier" name = "tier">
			<option value='bronze'>Bronze</option>
			<option value='silver'>Silver</option>
			<option value='gold'>Gold</option>
			<option value='platinum'>Platinum</option>
			<option value='diamond'>Diamond</option>
		</select>
		<input type="submit" name="selectSubmit"></p>
	</form>

	<hr />

	<h2>Ticket Purchased for Event</h2>
	<p>Find all customers that purchased tickets to the given event ID.</p>
	<form method="GET" action="customerservice.php">
		<input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
		Event ID: <input type="number" name="eventID"> <br /><br />

		<input type="submit" name="displayJoin"></p>
	</form>

	<hr />

	<h2>Greatest Number of Transactions</h2>
	<p>Find the customers that have made the greatest number of transactions.</p>
	<form method="GET" action="customerservice.php">
		<input type="hidden" id="displayTuplesRequest" name="displayTuplesRequest">
		<input type="submit" name="displayTicketCount"></p>
	</form>

	<?php

	function printTicketCountResult($result)
	{
		echo "<table>";
		echo "<tr><th>Customer ID</th><th>Number of Transactions</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
			echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
		}

		echo "</table>";
	}

	function printTierResult($result)
	{
		echo "<table>";
		echo "<tr><th>Customer ID</th><th>Customer Name</th><th>Customer Address</th><th>Customer Phone</th><th>Loyalty Points</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>";
		}

		echo "</table>";
	}

	function printJoinResult($result)
	{
		echo "<table>";
		echo "<tr><th>Customer ID</th><th>Customer Name</th><th>Customer Email</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>";
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
		echo "<br> All tables reset. <br>";
		
		oci_commit($db_conn);
	}

	function handleDisplayCustomerRequest()
	{
		global $db_conn;
		$result = executePlainSQL("SELECT c.custid, c.custname, c.custemail, c.loyaltypoints, ct.tier
								   FROM customer c, customertier ct
								   WHERE c.loyaltypoints = ct.loyaltypoints");
		echo "<table>";
		echo "<tr><th>Customer ID</th><th>Name</th><th>Email</th><th>Points</th><th>Tier</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
				echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>";
		}

		echo "</table>";
	}

	function handleJoinRequest()
	{
		global $db_conn;

		$eventID = $_GET['eventID'];

		$result = executePlainSQL(
			"SELECT distinct c.custid, c.custname, c.custemail
			FROM customer c, transaction tr, ticket ti
            WHERE c.custid = tr.custid and tr.transid = ti.transid and ti.eventid = $eventID"
		);

		printJoinResult($result);
	}

	function handleSelectRequest()
	{
		global $db_conn;
		$tier = $_POST['tier'];
		$result = executePlainSQL(
			"SELECT c.custid, c.custname, c.custaddress, c.custemail, c.loyaltypoints
			FROM customer c, customertier ct
			WHERE c.loyaltypoints = ct.loyaltypoints and ct.tier = '" . $tier . "'"
		);
		printTierResult($result);
	}

	function handleNestedAggregationRequest()
	{
		global $db_conn;
		$result = executePlainSQL(
			"SELECT t.custid, count(*) as count
			FROM transaction t
            GROUP BY t.custid
            HAVING COUNT(*) >= all (SELECT COUNT(*)
                                    FROM transaction t2
                                    GROUP BY t2.custid)"
		);
		printTicketCountResult($result);
	}

	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			} else if (array_key_exists('selectTuplesRequest', $_POST)) {
				handleSelectRequest();
			}

			disconnectFromDB();
		}
	}

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('displayJoin', $_GET)) {
				handleJoinRequest();
			} else if (array_key_exists('displayTicketCount', $_GET)) {
				handleNestedAggregationRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['selectSubmit']) || isset($_POST['reset'])) {
		handlePOSTRequest();
	} else if (isset($_GET['displayTuplesRequest'])) {
		handleGETRequest();
	}

	?>
</body>

</html>