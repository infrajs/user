<?php

use infrajs\db\Db;
use infrajs\path\Path;

$db = &Db::pdo();

$filesql = Path::theme('-user/update.sql');
$sql = file_get_contents($filesql);
$db->exec($sql);
