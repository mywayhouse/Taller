<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= PUBLIC_URL ?>/assets/css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><?= APP_NAME ?></h1>
                <p>Ingrese sus credenciales para acceder</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/auth/authenticate" method="POST" class="login-form">
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        name="correo"
                        id="correo"
                        value="<?= htmlspecialchars($oldCorreo) ?>"
                        placeholder="ej: admin@taller.com"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="contrasenia">Contraseña</label>
                    <input
                        type="password"
                        name="contrasenia"
                        id="contrasenia"
                        placeholder="Ingrese su contraseña"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>
</body>
</html>
