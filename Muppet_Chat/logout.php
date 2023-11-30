<?php
include_once "user.php";

session_start();
session_destroy();

header("Location: index.php");