<?php if (session_status() == PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?? 'Absensi Sekolah' ?></title>

    <!-- Font Awesome -->
    <link href="/absensi_smk1kadungora/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,700,900" rel="stylesheet">
    <!-- SB Admin 2 CSS -->
    <link href="/absensi_smk1kadungora/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- DataTables CSS (opsional jika dipakai) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
</head>
<body id="page-top">
<div id="wrapper">
