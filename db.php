<?php
$conn = new mysqli("localhost", "root", "", "ayura_hampers");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
