<?php
session_start();
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header("Location: index.php");
exit;
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
  icon: 'success',
  title: 'Sesión cerrada',
  text: 'Has cerrado sesión correctamente',
  timer: 2000,
  showConfirmButton: false
}).then(() => {
  window.location.href = "../index.php";
});
</script>
