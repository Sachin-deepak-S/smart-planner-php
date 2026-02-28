<?php
date_default_timezone_set("Asia/Kolkata");

include "../config/database.php";
include "../config/mail_config.php";

$today = date("Y-m-d");
$tomorrow = date("Y-m-d", strtotime("+1 day"));

echo "<h3>Reminder Engine Running...</h3>";

// -----------------------------
// RESET YEARLY FLAGS DAILY
// -----------------------------
mysqli_query($conn,
"UPDATE events 
 SET notified_today = 0, notified_before = 0
 WHERE repeat_type = 'yearly'");

// =============================
// 1️⃣ SAME-DAY NORMAL EVENTS
// =============================
$sql_today = "
SELECT e.*, u.email 
FROM events e
JOIN users u ON e.user_id = u.id
WHERE e.repeat_type = 'none'
AND e.event_date = '$today'
AND e.notified_today = 0
";

$result_today = mysqli_query($conn, $sql_today);

while ($row = mysqli_fetch_assoc($result_today)) {

    $subject = "Reminder: " . $row['title'];
    $message = "
        <h3>Event Today</h3>
        <b>" . $row['title'] . "</b><br>
        " . $row['description'] . "<br><br>
        Time: " . $row['event_time'];

    if (sendEmail($row['email'], $subject, $message)) {

        mysqli_query($conn,
        "UPDATE events SET notified_today = 1 WHERE id = " . $row['id']);

        echo "Sent same-day reminder for: " . $row['title'] . "<br>";
    }
}

// =============================
// 2️⃣ ONE-DAY-BEFORE NORMAL
// =============================
$sql_before = "
SELECT e.*, u.email 
FROM events e
JOIN users u ON e.user_id = u.id
WHERE e.repeat_type = 'none'
AND e.event_date = '$tomorrow'
AND e.notify_before = 1
AND e.notified_before = 0
";

$result_before = mysqli_query($conn, $sql_before);

while ($row = mysqli_fetch_assoc($result_before)) {

    $subject = "Upcoming Event Tomorrow: " . $row['title'];
    $message = "
        <h3>Event Tomorrow</h3>
        <b>" . $row['title'] . "</b><br>
        " . $row['description'];

    if (sendEmail($row['email'], $subject, $message)) {

        mysqli_query($conn,
        "UPDATE events SET notified_before = 1 WHERE id = " . $row['id']);

        echo "Sent tomorrow reminder for: " . $row['title'] . "<br>";
    }
}

// =============================
// 3️⃣ SAME-DAY YEARLY EVENTS
// =============================
$sql_yearly_today = "
SELECT e.*, u.email 
FROM events e
JOIN users u ON e.user_id = u.id
WHERE e.repeat_type = 'yearly'
AND DAY(e.event_date) = DAY('$today')
AND MONTH(e.event_date) = MONTH('$today')
AND e.notified_today = 0
";

$result_yearly_today = mysqli_query($conn, $sql_yearly_today);

while ($row = mysqli_fetch_assoc($result_yearly_today)) {

    $subject = "Yearly Reminder: " . $row['title'];
    $message = "
        <h3>Yearly Event Today</h3>
        <b>" . $row['title'] . "</b><br>
        " . $row['description'];

    if (sendEmail($row['email'], $subject, $message)) {

        mysqli_query($conn,
        "UPDATE events SET notified_today = 1 WHERE id = " . $row['id']);

        echo "Sent yearly today reminder for: " . $row['title'] . "<br>";
    }
}

// =============================
// 4️⃣ ONE-DAY-BEFORE YEARLY
// =============================
$sql_yearly_before = "
SELECT e.*, u.email 
FROM events e
JOIN users u ON e.user_id = u.id
WHERE e.repeat_type = 'yearly'
AND DAY(e.event_date) = DAY('$tomorrow')
AND MONTH(e.event_date) = MONTH('$tomorrow')
AND e.notify_before = 1
AND e.notified_before = 0
";

$result_yearly_before = mysqli_query($conn, $sql_yearly_before);

while ($row = mysqli_fetch_assoc($result_yearly_before)) {

    $subject = "Yearly Event Tomorrow: " . $row['title'];
    $message = "
        <h3>Yearly Event Tomorrow</h3>
        <b>" . $row['title'] . "</b><br>
        " . $row['description'];

    if (sendEmail($row['email'], $subject, $message)) {

        mysqli_query($conn,
        "UPDATE events SET notified_before = 1 WHERE id = " . $row['id']);

        echo "Sent yearly tomorrow reminder for: " . $row['title'] . "<br>";
    }
}

echo "<br>Reminder check completed.";
?>