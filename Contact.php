<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form fields and remove whitespace.
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Check that data was sent to the mailer.
    if (empty($name) OR empty($subject) OR empty($message) OR !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set a 400 (bad request) response code and exit.
        http_response_code(400);
        echo "Oops! There was a problem with your submission. Please complete the form and try again.";
        exit;
    }

    // Database configuration
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "socialcoder";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        http_response_code(500);
        echo "Connection failed: " . $conn->connect_error;
        exit;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO contactme (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt === false) {
        http_response_code(500);
        echo "Failed to prepare the SQL statement.";
        exit;
    }

    // Bind parameters
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the statement
    if ($stmt->execute()) {
        http_response_code(200);
        echo "Thank You! Your message has been sent and saved successfully.";
    } else {
        http_response_code(500);
        echo "Oops! Something went wrong and we couldn't save your message.";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    // Not a POST request, set a 403 (forbidden) response code.
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}
?>
