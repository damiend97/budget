<?php
	// display errors
	ini_set('display_errors', 1); error_reporting(~0);
	// start session
	session_start();

	// declare empty string for background
	$bg = "";

	// if user has not logged in...
	if (!isset($_SESSION['username'])) {
		// direct user to login page
		header("location: login.php");
		die();
	}

	else {
		// require connection
		require_once('connection.php');
		$username = mysqli_real_escape_string($dbCon, $_SESSION["username"]);

		// ---gender logic---
		// prepare SQL statement
		$sql = "SELECT gender FROM users WHERE username='$username'";
		// execute query
		$result = mysqli_query($dbCon, $sql);
		// grab data from query results
		$gender = mysqli_fetch_array($result);

		if ($gender[0] == "male") {
			$bg = "blue";
		} else {
			$bg = "pink";
		}

		// ----data logic----
		// data arrays
		$incomeNames = [];
		$incomeAmounts = [];
		$incomeDates = [];

		// prepare SQL statement
		$sql = "SELECT * FROM income WHERE user='$username' ORDER BY date";
		// execute query
		$result = mysqli_query($dbCon, $sql);

		// iterate through each row of results
		while($row = mysqli_fetch_array($result))
		{
			// push data to arrays
			array_push($incomeNames, $row[2]);
			array_push($incomeAmounts, $row[3]);
			array_push($incomeDates, $row[4]);
	   }

	   // expenses -----
		$expenseNames = [];
		$expenseAmounts = [];
		$expenseDates = [];

		// prepare SQL statement
		$sql = "SELECT * FROM expenses WHERE user='$username' ORDER BY date";
		// execute query
		$result = mysqli_query($dbCon, $sql);

		// iterate through each row of results
		while($row = mysqli_fetch_array($result))
		{
			// push data to arrays
			array_push($expenseNames, $row[2]);
			array_push($expenseAmounts, $row[3]);
			array_push($expenseDates, $row[4]);
	   }
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>DASHBOARD</title>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
	<style>
		* {
			margin: 0px;
			padding: 0px;
		}
		body {
			background: blue;
			color: white;
			height: 2000px;
		}
		nav {
			background: rgba(0,0,0,.4);
			padding: 10px;
			display: flex;
			align-items: center;
		}
		nav span {
			margin-left: auto;
		}
		.mainWrapper {
		}
		.incomeWrapper {
			margin-bottom: 10px;
		}
		.expenseWrapper {
			margin-bottom: 10px;
		}
		.rootHeader {
			background: white;
			color: #666;
			padding: 10px;
			display: flex;
			justify-content: space-between;
		}
		.rootWrapper {
			padding: 10px;
			text-align: center;
		}
		.element {
			margin-bottom: 30px;
		}
		.row {
			display: flex;
			justify-content: space-between;
			border-top: 1px dotted white;
			padding: 5px;
		}
		.row:last-child {
			border-bottom: 1px dotted white;
		}
		.col {
		}
		.tog {
			margin-left: auto;
			text-align: center;
			cursor: pointer;
		}
		#inForm, #exForm {
			display: none;
			text-align: center;
		}
		.overlayContainer {
			height: 100vh;
			width: 100%;
			position: fixed;
			top: 0;
			right: 0;
			left: 0;
			bottom: 0;
			background: rgba(0,0,0,.8);
			z-index: 1;
		}
		.overlayHeader {
			width: 100%;
			background: white;
			color: #333;
			padding: 15px;
			display: flex;
			justify-content: center;
		}
		.overlayHeader span {
			margin-left: auto;
			margin-right: 30px;
			cursor: pointer;
		}
		.overlayHeader span:hover {
			color: #666;
		}
		.overlayFlex {
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100%;
			width: 100%;
		}
		.overlayForm input {
			display: block;
			height: 50px;
			width: 80%;
			box-sizing: border-box;
			-moz-box-sizing: border-box;
			padding: 10px;
			text-align: center;
			background: none;
			border: 1px solid white;
			color: white;
			border-radius: 0px;
			margin-left: auto;
			margin-right: auto;
			margin-top: 10px;
		}
		#logout-link {
			cursor: pointer;
		}
		.del-but {
			padding: 5px;
			cursor: pointer;
		}
		.right {
			display: flex;
			justify-content: flex-end;
		}
	</style>
</head>
<body>
	<div id="inForm" class="overlayContainer">
		<div class="overlayHeader">
			Add Income Form
			<span id="closeIn"><i class="fas fa-window-close"></i></span>
		</div>
		<div class="overlayFlex">
			<form id="incomeForm" class="overlayForm" action="addIncome.php" method="POST">
				<input type="text" name="iName" placeholder="income name" required />
				<input type="number" step="0.01" min="0" name="iAmount" placeholder="amount" required />
				<input type="date" name="iDate" required />
				<input type="submit" value="Add Income" />
			</form>
		</div>
	</div>
	<div id="exForm" class="overlayContainer">
		<div class="overlayHeader">
			Add Expense Form
			<span id="closeEx"><i class="fas fa-window-close"></i></span>
		</div>
		<div class="overlayFlex">
			<form id="expenseForm" class="overlayForm" action="addExpense.php" method="POST">
				<input type="text" name="eName" placeholder="expense name" required />
				<input type="number" step="0.01" min="0" name="eAmount" placeholder="amount" required />
				<input type="date" name="eDate" required />
				<input type="submit" value="Add Expense" />
			</form>
		</div>
	</div>
	<!-- Navigation -->
	<nav>
		<img id="logout-link" src="logo.png" alt="logo" height="40px" width="40px" />
		<span><?php echo $_SESSION["username"]; ?></span>
	</nav>
	<!-- Main -->
	<div class="mainWrapper">
		<!-- Income -->
		<div class="incomeWrapper">
			<div class="rootHeader">
				income
				<i class="fas fa-plus tog tog-income"></i>
			</div>
			<div class="rootWrapper">
				<?php include("incometemp.php");?>
			</div>
		</div>
		<!-- Expenses -->
		<div class="expenseWrapper">
			<div class="rootHeader">
				expenses
				<i class="fas fa-plus tog tog-expense"></i>
			</div>
			<div class="rootWrapper">
			<?php include("expensetemp.php");?>
			</div>
		</div>
	</div>
	<script
		src="https://code.jquery.com/jquery-3.3.1.min.js"
		integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		crossorigin="anonymous">
	</script>
	<script>
		$(document).ready(function() {
			$("#logout-link").click(function() {
				var ask = window.confirm("Are you sure you want to logout?");

				if(ask) {
					window.location.href = "logout.php";
				}
			});
		});
	</script>
	<script>
		$(document).ready(function() {
			const bgColor = "<?php Print($bg); ?>";
			const bgStrg = "linear-gradient(to bottom, " + bgColor +", black) fixed"
			$('body').css("background",bgStrg);
		})
	</script>
	<script>
		$(document).ready(function() {
			// income
			$(".tog-income").click(function() {
				$("#inForm").fadeToggle(500);
			});

			$("#closeIn").click(function() {
				$("#inForm").fadeToggle(500);
			});

			// expenses
			$(".tog-expense").click(function() {
				$("#exForm").fadeToggle(500);
			});

			$("#closeEx").click(function() {
				$("#exForm").fadeToggle(500);
			});
		})
	</script>
	<script>
		function getTextNodesIn(el) {
			return $(el).find(":not(iframe)").addBack().contents().filter(function() {
				return this.nodeType == 3;
			});
		};

		$(".iDel").click(function(e) {
			var ask = window.confirm("Are you sure you want to delete this item?");

			if(ask) {
				let parent = $(this).parent();
				let grandpa = parent.parent();

				let children = grandpa.children("div");

				let childOne = $(children[1]).children("div").eq(1);
				let childTwo = $(children[2]).children("div").eq(1);
				let childThree = $(children[3]).children("div").eq(1);

				let name = getTextNodesIn(childOne).text();
				let amount = getTextNodesIn(childTwo).text();
				let date = getTextNodesIn(childThree).text();

				$.ajax({
					type: "POST",
					url: 'deleteIncome.php',
					data: {
						name: name,
						amount: amount,
						date: date
					},
					success: function(data){
						location.reload();
					}
				});
			}

		});

		$(".eDel").click(function(e) {
			var ask = window.confirm("Are you sure you want to delete this item?");

			if (ask) {
				let parent = $(this).parent();
				let grandpa = parent.parent();

				let children = grandpa.children("div");

				let childOne = $(children[1]).children("div").eq(1);
				let childTwo = $(children[2]).children("div").eq(1);
				let childThree = $(children[3]).children("div").eq(1);

				let name = getTextNodesIn(childOne).text();
				let amount = getTextNodesIn(childTwo).text();
				let date = getTextNodesIn(childThree).text();

				$.ajax({
					type: "POST",
					url: 'deleteExpense.php',
					data: {
						name: name,
						amount: amount,
						date: date
					},
					success: function(data){
						location.reload();
					}
				});
			}
		});
	</script>
</body>
</html>