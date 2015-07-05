<?php
$db=new PDO('mysql:dbname=govhack;host=127.0.0.1','govhack', '');

session_set_cookie_params(60*60*24*365*10);
session_start();

$returnJSON = array();

/*if(isset($_GET['getLibraries'])) {
}*/

if(isset($_GET['getMyID'])){
	if(!isset($_SESSION['userID'])) {
		$insertStatement = $db->prepare('INSERT INTO users (name, description) VALUES (?,?)');
		$result = $insertStatement->execute(array('Guest'.rand(1,1000), "I am too lazy to write a description :("));
		$_SESSION['userID'] = $db->lastInsertId();
	}
	
	$returnJSON['getMyID'] = $_SESSION['userID'];
}

if(isset($_GET['updateProfile'])) {
	$insertStatement = $db->prepare('UPDATE users SET name = ?, description=?, bestbook=? WHERE id = ?');
	$result = $insertStatement->execute(
		array($_POST['name'], $_POST['description'], $_POST['bestbook'], $_SESSION['userID'])
	);
	
	$returnJSON['updateProfile'] = true;
}

if(isset($_GET['getUsers'])) {
	$statement = $db->prepare('SELECT * from users');
	$statement->execute();
	$rowArr = array();
	while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		$rowArr[] = $row;
	}
	$returnJSON['getUsers'] = $rowArr;
}



if(isset($_GET['getLibraries'])) {
	$statement = $db->prepare('SELECT * from libraries');
	$statement->execute();
	$rowArr = array();
	while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		$rowArr[] = $row;
	}
	$returnJSON['getLibraries'] = $rowArr;
}


if(isset($_GET['getMsgs'])) {
	$statement = $db->prepare('SELECT messages.*, messages.id as msgID, users.id, users.username  FROM messages, users WHERE users.id = messages.userID AND messages.id >= ? AND roomID = ? ORDER BY date ASC');
	$statement->execute(array($_POST['minID'], $_POST['room']));	
	$rowArr = array();
	while ($row = $statement->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
		$rowArr[] = $row;
	}
	$returnJSON['getMsgs'] = $rowArr;
}
echo json_encode($returnJSON);
?>