<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Tienda Online'; ?></title>
    <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL']; ?>/css/styleHeader.css">
    <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL']; ?>/css/styleFooter.css">
    <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL']; ?>/css/home.css">
    <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL']; ?>/css/styleUserWelcome.css">
    <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL']; ?>/css/errors.css">
    <!-- <link rel="stylesheet" href="<?php echo $_ENV['BASE_URL']; ?>/css/styleFormLogin.css"> -->
</head>
<body>
    <!-- Header Navigation -->
    <?php if (isset($showHeader) && $showHeader === true): ?>
        <?php include __DIR__ . '/../shared/header.php'; ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php include $contentFile; ?>
    </main>
    
    <!-- Footer -->
    <?php if (isset($showFooter) && $showFooter === true): ?>
        <?php include __DIR__ . '/../shared/footer.php'; ?>
    <?php endif; ?>
    
    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
    <!-- Header Navigation Script -->
    <script src="<?php echo $_ENV['BASE_URL']; ?>/js/scriptHeader.js"></script>
</body>
</html>
