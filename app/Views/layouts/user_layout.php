<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
</head>
<body>
    <header>
        <h1>Admin Page</h1>
    </header>
    <main id="app">
        <?= $this->renderSection('content') ?>
    </main>
    <footer>
        <p>&copy; 2024 Admin Page</p>
    </footer>
</body>
</html>
