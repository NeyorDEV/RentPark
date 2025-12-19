<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connection</title>
    <script>
    (function () {
        try {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        } catch (e) { }
    })();
    </script>
  <link rel="stylesheet" href="/siteSAE2A/html/css/menu.css">
  <link rel="stylesheet" href="/siteSAE2A/html/css/parametres.css">
  <link rel="stylesheet" href="html/css/inscription.css">
  <link rel="icon" type="image/png" href="html/icons/voiture.png">
 <link rel="shortcut icon" href="html/icons/favicon.ico" type="image/x-icon">
</head>
<body>
  <div class="login-container">
    <h2>Se connecter</h2>

    <form action="/siteSAE2A/connection" method="POST">

      <input type="hidden" name="action" value="connection">

      <div class="input-group">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="input-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" name="connection">Se Connecter</button>

      <?php if (isset($_GET['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <p class="success"><?php echo htmlspecialchars($_GET['success']); ?></p>
      <?php endif; ?>
    </form>

    <p style="margin-top: 1rem;">
      Pas de compte ? <a href="/siteSAE2A/inscription">S'inscrire</a>
    </p>
  </div>
</body>
</html>
