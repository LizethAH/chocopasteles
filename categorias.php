<?php
session_start();
require_once("config/database.php");
// Obtener categorías para el menú
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");

// Obtener categoría seleccionada
$categoria_id = isset($_GET['cat']) ? intval($_GET['cat']) : 0;

// Si hay categoría seleccionada, filtrar productos
if ($categoria_id > 0) {
    $stmt = $conn->prepare("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.categoria_id = ? ORDER BY p.id DESC");
    $stmt->bind_param("i", $categoria_id);
    $stmt->execute();
    $productos = $stmt->get_result();
    $stmt->close();
} else {
    // Si no, mostrar todos los productos
    $productos = $conn->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Chocopasteles - Inicio</title>
  <?php include __DIR__ . "/includes/head.php"; ?>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<header>
   <?php include("includes/header.php"); ?>
</header>
<main>
    <h1>
        <?php
        if ($categoria_id > 0) {
            $catname = $conn->query("SELECT nombre FROM categorias WHERE id=$categoria_id")->fetch_assoc();
            echo "Categoría: " . htmlspecialchars($catname['nombre']);
        } else {
            echo "Todas las categorías";
        }
        ?>
    </h1>
    <div class="productos-lista">
        <?php while($prod = $productos->fetch_assoc()): ?>
        <div class="producto-card">
            <?php if ($prod['imagen']): ?>
                <img src="assets/img/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>" width="180">
            <?php endif; ?>
            <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
            <t1><?= htmlspecialchars($prod['descripcion']) ?></t1>
            <span class="precio">S/ <?= number_format($prod['precio'], 2) ?></span>
            <button class="agregar-carrito" data-id="<?= $prod['id'] ?>">Agregar al carrito</button>
        </div>
        <?php endwhile; ?>
    </div>
</main>
<footer style="background:#5D4037;color:#fff;margin-top:40px;"> 
<?php include("includes/footer.php"); ?>
</footer>


<!-- Carrito flotante -->
<div id="carrito-flotante">
  <div id="carrito-header">
    <span>🛒 Carrito (<span id="carrito-cantidad">0</span>)</span>
    <button id="carrito-cerrar">×</button>
  </div>
  <div id="carrito-lista"></div>
  <div id="carrito-total"></div>
  <button id="carrito-whatsapp">Enviar por WhatsApp</button>
</div>
<button id="carrito-toggle">🛒</button>

<script src="assets/js/main.js"></script>

<script>
// Productos desde PHP para JS
const productosData = {};
<?php
$prods = $conn->query("SELECT id, nombre, precio FROM productos");
while($p = $prods->fetch_assoc()):
?>
productosData[<?= $p['id'] ?>] = {
  nombre: "<?= addslashes($p['nombre']) ?>",
  precio: <?= $p['precio'] ?>
};
<?php endwhile; ?>

let carrito = JSON.parse(localStorage.getItem('carritoChoco')) || {};

function actualizarCarrito() {
  let lista = '';
  let total = 0;
  let cantidad = 0;
  for (let id in carrito) {
    let prod = productosData[id];
    let cant = carrito[id];
    total += prod.precio * cant;
    cantidad += cant;
    lista += `<div>
      <b>${prod.nombre}</b> x${cant} - S/ ${(prod.precio * cant).toFixed(2)}
      <button onclick="cambiarCantidad(${id},-1)">-</button>
      <button onclick="cambiarCantidad(${id},1)">+</button>
      <button onclick="eliminarProducto(${id})">🗑️</button>
    </div>`;
  }
  document.getElementById('carrito-lista').innerHTML = lista || '<em>Carrito vacío</em>';
  document.getElementById('carrito-total').innerHTML = 'Total: S/ ' + total.toFixed(2);
  document.getElementById('carrito-cantidad').textContent = cantidad;
}
function cambiarCantidad(id, delta) {
  carrito[id] = (carrito[id] || 0) + delta;
  if (carrito[id] <= 0) delete carrito[id];
  localStorage.setItem('carritoChoco', JSON.stringify(carrito));
  actualizarCarrito();
}
function eliminarProducto(id) {
  delete carrito[id];
  localStorage.setItem('carritoChoco', JSON.stringify(carrito));
  actualizarCarrito();
}
document.querySelectorAll('.agregar-carrito').forEach(btn => {
  btn.onclick = () => {
    let id = btn.dataset.id;
    carrito[id] = (carrito[id] || 0) + 1;
    localStorage.setItem('carritoChoco', JSON.stringify(carrito));
    actualizarCarrito();
    document.getElementById('carrito-flotante').style.display = 'block';
  };
});
document.getElementById('carrito-toggle').onclick = () => {
  document.getElementById('carrito-flotante').style.display = 'block';
};
document.getElementById('carrito-cerrar').onclick = () => {
  document.getElementById('carrito-flotante').style.display = 'none';
};
document.getElementById('carrito-whatsapp').onclick = () => {
  let mensaje = "¡Hola! Quiero pedir:\n";
  for (let id in carrito) {
    let prod = productosData[id];
    mensaje += `• ${prod.nombre} x${carrito[id]} - S/ ${(prod.precio * carrito[id]).toFixed(2)}\n`;
  }
  mensaje += "\nTotal: S/ " + Object.keys(carrito).reduce((t, id) => t + productosData[id].precio * carrito[id], 0).toFixed(2);
  window.open("https://wa.me/59164939922?text=" + encodeURIComponent(mensaje), "_blank");
  carrito = {};
  localStorage.removeItem('carritoChoco');
  actualizarCarrito();
  document.getElementById('carrito-flotante').style.display = 'none';
};
actualizarCarrito();
</script>
<?php include __DIR__ . "/includes/adminModal.php"; ?>

</body>
</html>