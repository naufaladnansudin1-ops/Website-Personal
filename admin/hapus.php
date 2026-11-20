<?php
require_once 'auth.php'; require_once '../koneksi.php';
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT);if(!$id)die('ID tidak valid.');
$s=$conn->prepare("SELECT foto FROM rumah_makan WHERE id=?");$s->bind_param("i",$id);$s->execute();$r=$s->get_result()->fetch_assoc();
$d=$conn->prepare("DELETE FROM rumah_makan WHERE id=?");$d->bind_param("i",$id);
if($d->execute() && $r && $r['foto'] && is_file(__DIR__.'/../assets/uploads/'.basename($r['foto'])))@unlink(__DIR__.'/../assets/uploads/'.basename($r['foto']));
header('Location: rumah_makan.php');exit;
?>
