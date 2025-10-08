<?php
require 'init.php';
session_unset();
session_destroy();
header('Location: login_form.php');
exit;
