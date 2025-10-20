<?php
session_start();
require_once("config/database.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <title>Chocopasteles - Inicio</title>
  <?php include __DIR__ . "/includes/head.php"; ?>
</head>
<body>
<header>
   <?php include("includes/header.php"); ?>
   <link rel="stylesheet" href="assets/css/styles.css">
</header>
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
<main class="contacto-main">
  <section class="contacto-header">
    <h2>Estamos aquí para ti 🍰</h2>
    <p>Queremos endulzar tus momentos especiales, contáctanos y déjanos ser parte de tu celebración.</p>
  </section>

  <section class="contacto-info">
    <div class="contacto-card">
      <i class="fas fa-phone"></i>
      <h3>Teléfono</h3>
      <p>+591 64939922</p>
    </div>

    <div class="contacto-card">
      <i class="fas fa-envelope"></i>
      <h3>Correo</h3>
      <p>info@chocopasteles.com</p>
    </div>

    <div class="contacto-card">
      <i class="fas fa-map-marker-alt"></i>
      <h3>Ubicación</h3>
      <p>Warnes, Satelite Norte, Cancha sintetica alado</p>
    </div>
  </section>

  <section class="contacto-motiva">
    <h2>💬 ¿Tienes dudas?</h2>
    <p>Escríbenos por WhatsApp y te atenderemos al instante.</p>
    <a href="https://wa.me/59164939922" class="btn-whatsapp">Hablar por WhatsApp</a>
  </section>
</main>

<footer style="background:#5D4037;color:#fff;">
<?php include("includes/footer.php"); ?>
</footer>

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
<script>
document.getElementById('adminLoginBtnFooter')?.addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('adminLoginModal').style.display = 'flex';
});
</script>
 <?php include __DIR__ . "/includes/adminModal.php"; ?>
</body>
</html>