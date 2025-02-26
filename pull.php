<?php
// Replace these variables with your own
$organization = 'tisi-devops';
$project = 'test';
$repository = 'test';
$pat = '6b3PRQ5sr3sNVLgSs9tqUFOBq5u3uqRMURyApZi7sMFISD2aecg3JQQJ99BBACAAAAAH9gM8AAASAZDO20UP';
$username = 'devops.tisi.mail.go.th';

// Base64 encode the PAT
$encoded_pat = base64_encode(':' . $pat);

// Set the API URL for the pull request
$url = "https://dev.azure.com/$organization/$project/_apis/git/repositories/$repository/pullrequests?api-version=6.0";

// Initialize cURL session for the pull request
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Basic $encoded_pat",
    "Content-Type: application/json"
]);

// Execute cURL request for the pull request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // Process the response
    $data = json_decode($response, true);
    print_r($data);
}

// Close cURL session
curl_close($ch);

// Function to execute Git commands and log status
function executeGitCommand($command) {
    exec($command, $output, $return_var);
    if ($return_var !== 0) {
        throw new Exception(implode("\n", $output));
    }
    return $output;
}

// Clone the repository (only needed once)
//$cloneCommand = "git clone https://$username:$pat@dev.azure.com/$organization/$project/_git/$repository";
//executeGitCommand($cloneCommand);

// Navigate to the cloned repository directory
chdir($repository);

// Pull the latest changes
$pullCommand = "git pull";
try {
    $pullOutput = executeGitCommand($pullCommand);
    echo "Pull successful!";
    print_r($pullOutput);
} catch (Exception $e) {
    echo "Pull failed: " . $e->getMessage();
}

// Create a log file
$logFile = 'git_pull_status.log';
$logMessage = date('Y-m-d H:i:s') . " - Pull status: " . (isset($pullOutput) ? 'Success' : 'Failure') . "\n";
file_put_contents($logFile, $logMessage, FILE_APPEND);
?>