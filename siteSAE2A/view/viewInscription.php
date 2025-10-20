<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <link rel="stylesheet" href="../html/css/inscription.css">
</head>
<body>
  <div class="login-container">
    <h2>Créer un compte</h2>

    <form action="../index.php" method="POST">

      <input type="hidden" name="action" value="inscription">

      <div class="input-group">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" id="username" name="username" required>
      </div>

      <div class="input-group">
        <label for="password">Mot de passe</label>
        <input type="password" id="password" name="password" required>
      </div>

      <div class="input-group">
        <label for="confirm">Confirmer le mot de passe</label>
        <input type="password" id="confirm" name="confirm" required>
      </div>

      
      <div class="input-group">
        <label>Rôle</label>
        <div class="role-options">
          <label>
            <input type="radio" name="role" value="admin" required>
            Admin
          </label>
          <label>
            <input type="radio" name="role" value="employe">
            Employé
          </label>
          <label>
            <input type="radio" name="role" value="client">
            Client
          </label>
        </div>
      </div>

      <button type="submit" name="inscritpion">S'inscrire</button>

      <?php if (isset($_GET['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
      <?php endif; ?>

      <?php if (isset($_GET['success'])): ?>
        <p class="success"><?php echo htmlspecialchars($_GET['success']); ?></p>
      <?php endif; ?>
    </form>

    <p style="margin-top: 1rem;">
      Déjà inscrit ? <a href="viewConnection.php">Se connecter</a>
    </p>
  </div>
</body>
</html>
