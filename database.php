<?php
try
{
	$db = new PDO("pgsql:dbname=ladder host=10.90.12.46/314-ladder password="2345349854" user=bitnami");
  // Password changed to a generic one from original file (in private repo) which had my personal password
  
	$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
}
catch (PDOException $err)
{
	exit(json_encode(["error_text" => "Database error: " . $err->getMessage()]));
}

?>
