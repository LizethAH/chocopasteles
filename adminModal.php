<?php
// 🔹 Iniciar sesión solo si aún no se ha iniciado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔹 Conectar con la base de datos
require_once(__DIR__ . "/../config/database.php");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_admin'])) {
    $email = trim($_POST['email'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo inválido.";
    } elseif ($clave === '') {
        $error = "Ingrese la contraseña.";
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE email=? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Compatibilidad get_result / bind_result
            $row = null;
            if (method_exists($stmt, 'get_result')) {
                $res = $stmt->get_result();
                $row = $res->fetch_assoc() ?: null;
            } else {
                $stmt->bind_result($r_id, $r_password);
                if ($stmt->fetch()) {
                    $row = ['id' => $r_id, 'password' => $r_password];
                }
            }

            // Depuración temporal en log (no mostrar en pantalla)
            if ($row) {
                error_log("Admin login: email={$email} - encontrado id={$row['id']} - pass_len=" . strlen($row['password']));
            } else {
                error_log("Admin login: email={$email} - NO encontrado");
            }

            if ($row) {
                $hash = $row['password'];
                if (password_verify($clave, $hash)) {
                    $_SESSION['admin'] = $row['id'];

                    // redirigir a la ruta correcta del proyecto (soporta subcarpeta)
                    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
                    $redirect = $base . '/admin/dashboard.php';
                    echo "<script>window.location.href=" . json_encode($redirect) . ";</script>";
                    exit;
                } else {
                    $error = "Contraseña incorrecta.";
                    error_log("Admin login: contraseña incorrecta para email={$email}");
                }
            } else {
                $error = "El correo no está registrado.";
            }

            $stmt->close();
        } else {
            $error = "Error en la consulta: " . $conn->error;
            error_log("Admin login: prepare falló - " . $conn->error);
        }
    }
}
?>

<!-- 🔹 Modal de login de administrador -->
<div id="adminLoginModal" style="display:none;position:fixed;z-index:2000;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.4);align-items:center;justify-content:center;">
    <div style="background:#fff;padding:30px 24px;border-radius:12px;max-width:340px;width:90%;margin:auto;position:relative;">
        <button onclick="document.getElementById('adminLoginModal').style.display='none'"
                style="position:absolute;top:8px;right:12px;background:none;border:none;font-size:1.5em;color:#5D4037;">×</button>
        <h2 style="color:#5D4037;text-align:center;">Acceso Administrador</h2>

        <form method="POST" autocomplete="off">
            <input type="hidden" name="login_admin" value="1">
            <label style="display:block;margin:8px 0;font-weight:600;">Correo</label>
            <input name="email" type="email" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <label style="display:block;margin:8px 0;font-weight:600;">Contraseña</label>
            <input name="clave" type="password" required style="width:100%;padding:8px;border:1px solid #ddd;border-radius:6px;">
            <button type="submit" style="margin-top:12px;width:100%;padding:10px;border:none;background:#5D4037;color:#fff;border-radius:8px;">Ingresar</button>
        </form>

        <?php if (!empty($error)) : ?>
            <div style="color:#c00;margin-top:10px;text-align:center;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
</div>
