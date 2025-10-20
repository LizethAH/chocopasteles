<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}
require_once("../config/database.php");

// Obtener categorías para el select
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");

// Agregar producto
if (isset($_POST['nuevo_producto'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $categoria_id = intval($_POST['categoria_id']);
    $imagen = "";

    // Subir imagen si se seleccionó
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $dir = "../assets/img/";
        $nombre_img = time() . "_" . basename($_FILES['imagen']['name']);
        $ruta = $dir . $nombre_img;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            $imagen = $nombre_img;
        }
    }

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen, $categoria_id);
    $stmt->execute();
    $stmt->close();
    header("Location: productos.php");
    exit;
}

// Editar producto
if (isset($_POST['editar_producto'])) {
    $id = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $categoria_id = intval($_POST['categoria_id']);
    $imagen = $_POST['imagen_actual'];

    // Subir nueva imagen si se seleccionó
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $dir = "../assets/img/";
        $nombre_img = time() . "_" . basename($_FILES['imagen']['name']);
        $ruta = $dir . $nombre_img;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta)) {
            $imagen = $nombre_img;
        }
    }

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, categoria_id=? WHERE id=?");
    $stmt->bind_param("ssdsii", $nombre, $descripcion, $precio, $imagen, $categoria_id, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: productos.php");
    exit;
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    // Eliminar imagen del servidor
    $res = $conn->query("SELECT imagen FROM productos WHERE id=$id");
    if ($row = $res->fetch_assoc()) {
        if ($row['imagen'] && file_exists("../assets/img/" . $row['imagen'])) {
            unlink("../assets/img/" . $row['imagen']);
        }
    }
    $stmt = $conn->prepare("DELETE FROM productos WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: productos.php");
    exit;
}

// Obtener todos los productos
$productos = $conn->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos - Admin Chocopasteles</title>
    <link rel="stylesheet" href="../assets/css/styles.php">
</head>
<body>
<?php include("includes/sidebar.php"); ?>
<main class="dashboard">
    <h2>Productos</h2>
    <form method="POST" enctype="multipart/form-data" style="margin-bottom:20px;">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="number" step="0.01" name="precio" placeholder="Precio" required>
        <select name="categoria_id" required>
            <option value="">Selecciona categoría</option>
            <?php while($cat = $categorias->fetch_assoc()): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="file" name="imagen" accept="image/*" required>
        <button type="submit" name="nuevo_producto">Agregar producto</button>
    </form>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Precio</th>
            <th>Categoría</th>
            <th>Acciones</th>
        </tr>
        <?php while($prod = $productos->fetch_assoc()): ?>
        <tr>
            <form method="POST" enctype="multipart/form-data">
                <td><?= $prod['id'] ?></td>
                <td>
                    <?php if ($prod['imagen']): ?>
                        <img src="../assets/img/<?= htmlspecialchars($prod['imagen']) ?>" width="60">
                    <?php endif; ?>
                    <input type="file" name="imagen" accept="image/*">
                </td>
                <td>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($prod['nombre']) ?>" required>
                </td>
                <td>
                    <textarea name="descripcion" required><?= htmlspecialchars($prod['descripcion']) ?></textarea>
                </td>
                <td>
                    <input type="number" step="0.01" name="precio" value="<?= $prod['precio'] ?>" required>
                </td>
                <td>
                    <select name="categoria_id" required>
                        <?php
                        // Volver a cargar categorías para cada fila
                        $cats = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
                        while($cat = $cats->fetch_assoc()):
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $prod['categoria_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                    <input type="hidden" name="imagen_actual" value="<?= htmlspecialchars($prod['imagen']) ?>">
                    <button type="submit" name="editar_producto">Editar</button>
                    <a href="?eliminar=<?= $prod['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</main>
</body>
</html>