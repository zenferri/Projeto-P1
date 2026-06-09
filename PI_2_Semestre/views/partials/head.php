<?php
$titulo = $titulo ?? "Singularys";
$descricao = $descricao ?? "Portal Singularys para contratação e operação de VPS.";
$layoutArea = $layoutArea ?? "landpage"; // se o layoutArea não for definido, define como landpage.
$cssExtra = $cssExtra ?? [];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($descricao); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#101C5E">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
          crossorigin="anonymous">

    <?php if ($layoutArea === "app") { ?>
        <link rel="stylesheet" href="./css/app.css?v=20260522">
        <style>:root { color-scheme: light only; }</style>
    <?php } else { ?>
        <style>
            :root { color-scheme: light only; }
            html, body { background: #fff; color: #101C5E; }
        </style>
        <link rel="stylesheet" href="./css/style5.css?v=23112025" id="themeStylesheet">
        <link rel="stylesheet" href="./css/style_login.css?v=23112025">
        <link rel="stylesheet" href="./css/botoes.css?v=23112025">
        <link rel="stylesheet" href="./css/reveal.css?v=23112025">
        <link rel="stylesheet" href="./css/smallscreen.css?v=23112025">
        <?php foreach ($cssExtra as $arquivoCss) { ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($arquivoCss); ?>">
        <?php } ?>
    <?php } ?>
</head>
<body class="<?php echo $layoutArea === "app" ? "app-body" : ""; ?>">
