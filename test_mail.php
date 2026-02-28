<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include "config/mail_config.php";

if(sendEmail("sachindeepak4181@gmail.com", "Test Email", "It works! 🚀")) {
    echo "Email Sent Successfully!";
} else {
    echo "Email Failed!";
}