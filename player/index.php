<?php
include '../database.php';
include '../rest.php';
include '../verify.php';

// Get incoming request, its type, and its variables
$request = new RestRequest();
$method = $request->getRequestType();
$request_vars = $request->getRequestVariables(); 

// Throw exception if email or phone number is invalid
if (!validatePhoneNumber($request_vars["phone"])) {
    exit(json_encode(["error_text" => "Database error: Invalid phone number."]);
} else if (!validateEmail($request_vars["email"])) {
    exit(json_encode(["error_text" => "Database error: Invalid email address."]);
}

// Create response to request
$response = ["resource"=>"player", "method"=>$method];
$response["resource"] = "player";
$response["method"] = $method;
echo json_encode($response);

if($request->isPost()) {
    
    // Query to ask how many players there are
    $sql = "Select count(username) from player";
    $statement = $db->prepare($sql);
    $statement->execute();
    
    // Get results of player count query
    $playerCount = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    // Add a new player, using playerCount + 1 as the rank
    $sql = "Insert into player(name, email, rank, phone, username, password)
            values(?, ?, ?, ?, ?, ?)";
    $statement = $db->prepare($sql);
    $statement->execute([$request_vars["name"], $request_vars["email"], 
                         ($playerCount + 1), $request_vars["phone"],
                         $request_vars["username"], $request_vars["password"]]);
}

else if($request->isGet()) {
    $sql = "Select * from player where username = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$request_vars["username"]]);
}

else if($request->isPut()) {
    $sql = "Update player set name = ?, rank = ?, email = ?, phone = ?
        where username = ?";
    $statement = $db->prepare($sql);
    $statement->execute([$request_vars["name"], $request_vars["rank"], 
                         $request_vars["email"], $request_vars["phone"],]
                         $request_vars["username"]]);
}

else if($request->isDelete()) {
    
    // Query to get rank of player you're about to delete
    $sql = "Select rank from player where username = ?";
    $statement = $db->prepare($sql);
    $statement->execute($request_vars["username"]);
    
    // Get results of player rank query
    $playerRank = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    // Throw exception if there is not a player to delete
    if(empty($playerRank)) {
        exit(json_encode(["error_text" => "Database error: No such player in database"]);
    }
    
    // Delete player from database
    $sql = "Delete from player where username = ?";
    $statement = $db->prepare($sql);
    $statement->execute($request_vars["username"]);
    
    // Get every player with a lower rank than deleted player
    $sql = "Select rank from player where rank > $playerRank";
    $statement = $db->prepare($sql);
    $statement->execute();
    
    // Get results of player rank query
    $ranks = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    // Decrement the rank of every player from previous query
    foreach($ranks as $playerRank) {
        $sql = "Update player set rank = ? where rank = $playerRank";
        $statement = $db->prepare($sql);
        $statement->execute($request_vars["rank"] - 1);
    }
}
