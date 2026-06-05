<?php
session_start();
require_once __DIR__ . '/../app/config.php';

session_destroy();
session_start();
flash_set('Anda telah berhasil logout.', 'success');
redirect_to('/index.php');
