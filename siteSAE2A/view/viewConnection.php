<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <link rel="stylesheet" href="../html/css/inscription.css">
</head>
<body>
  <div class="login-container">
    <h2>Se connecter</h2>

    <form action="../index.php" method="POST">

      <input type="hidden" name="action" value="connection">

      <div class="input-group">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="input-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
      </div>

      <button type="submit" name="inscritpion">Se Connecter</button>

      <?php if (isset($_GET['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <p class="success"><?php echo htmlspecialchars($_GET['success']); ?></p>
      <?php endif; ?>
    </form>

    <p style="margin-top: 1rem;">
      Pas de compte ? <a href="viewInscription.php">S'inscrire</a>
    </p>
  </div>
</body>
</html>
