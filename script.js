// Datos iniciales de partidos
let partidos = [
    {
        id: 1,
        equipoLocal: "Los Tigres",
        equipoVisitante: "Águilas Doradas",
        fecha: "2025-10-20",
        hora: "15:00",
        categoria: "Sub-15",
        lugar: "Cancha Principal"
    },
    {
        id: 2,
        equipoLocal: "Leones FC",
        equipoVisitante: "Halcones United",
        fecha: "2025-10-22",
        hora: "16:30",
        categoria: "Sub-17",
        lugar: "Cancha Principal"
    },
    {
        id: 3,
        equipoLocal: "Pumas Azules",
        equipoVisitante: "Cóndores FC",
        fecha: "2025-10-25",
        hora: "14:00",
        categoria: "Sub-13",
        lugar: "Cancha Secundaria"
    },
    {
        id: 4,
        equipoLocal: "Dragones Rojos",
        equipoVisitante: "Lobos Grises",
        fecha: "2025-10-27",
        hora: "15:30",
        categoria: "Sub-15",
        lugar: "Cancha Principal"
    },
    {
        id: 5,
        equipoLocal: "Búhos Nocturnos",
        equipoVisitante: "Panteras Negras",
        fecha: "2025-10-29",
        hora: "17:00",
        categoria: "Sub-17",
        lugar: "Cancha Principal"
    },
    {
        id: 6,
        equipoLocal: "Zorros Plateados",
        equipoVisitante: "Jaguares Salvajes",
        fecha: "2025-11-01",
        hora: "16:00",
        categoria: "Sub-13",
        lugar: "Cancha Secundaria"
    }
];

// Datos iniciales de equipos
let equipos = [
    {
        id: 1,
        nombre: "Los Tigres",
        categoria: "Sub-15",
        capitan: "Carlos Rodríguez",
        color: "#f97316"
    },
    {
        id: 2,
        nombre: "Águilas Doradas",
        categoria: "Sub-15",
        capitan: "María González",
        color: "#eab308"
    },
    {
        id: 3,
        nombre: "Leones FC",
        categoria: "Sub-17",
        capitan: "Pedro Martínez",
        color: "#dc2626"
    },
    {
        id: 4,
        nombre: "Halcones United",
        categoria: "Sub-17",
        capitan: "Ana López",
        color: "#0ea5e9"
    }
];

// Datos de la tabla de posiciones
let tablaPosiciones = [
    { pos: 1, equipo: "Los Tigres", pj: 8, pg: 6, pe: 1, pp: 1, pts: 19 },
    { pos: 2, equipo: "Águilas Doradas", pj: 8, pg: 5, pe: 2, pp: 1, pts: 17 },
    { pos: 3, equipo: "Leones FC", pj: 8, pg: 5, pe: 1, pp: 2, pts: 16 },
    { pos: 4, equipo: "Halcones United", pj: 8, pg: 4, pe: 3, pp: 1, pts: 15 },
    { pos: 5, equipo: "Pumas Azules", pj: 8, pg: 4, pe: 2, pp: 2, pts: 14 },
    { pos: 6, equipo: "Cóndores FC", pj: 8, pg: 3, pe: 3, pp: 2, pts: 12 },
    { pos: 7, equipo: "Dragones Rojos", pj: 8, pg: 3, pe: 2, pp: 3, pts: 11 },
    { pos: 8, equipo: "Lobos Grises", pj: 8, pg: 2, pe: 3, pp: 3, pts: 9 }
];

// Función para alternar el menú móvil
function toggleMenu() {
    const navMenu = document.querySelector('.nav-menu');
    navMenu.classList.toggle('active');
}

// Función para formatear fecha
function formatearFecha(fecha) {
    const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES', opciones);
}

// Función para renderizar partidos
function renderizarPartidos() {
    const container = document.getElementById('partidosContainer');
    if (!container) return;

    container.innerHTML = partidos.map(partido => `
        <div class="partido-card">
            <div class="partido-header">
                <div class="partido-fecha">
                    <i class="fas fa-calendar-day"></i>
                    <span>${formatearFecha(partido.fecha)}</span>
                </div>
                <span class="partido-categoria">${partido.categoria}</span>
            </div>
            <div class="partido-equipos">
                <div class="equipo">
                    <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #1e40af;"></i>
                    <div class="equipo-nombre">${partido.equipoLocal}</div>
                </div>
                <div class="vs">VS</div>
                <div class="equipo">
                    <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #f59e0b;"></i>
                    <div class="equipo-nombre">${partido.equipoVisitante}</div>
                </div>
            </div>
            <div class="partido-horario">
                <i class="fas fa-clock"></i>
                <strong>${partido.hora}</strong> - ${partido.lugar}
            </div>
        </div>
    `).join('');
}

// Función para renderizar equipos
function renderizarEquipos() {
    const container = document.getElementById('equiposContainer');
    if (!container) return;

    if (equipos.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #6b7280; grid-column: 1/-1;">No hay equipos registrados. ¡Crea el primero!</p>';
        return;
    }

    container.innerHTML = equipos.map(equipo => `
        <div class="equipo-card" style="border-left-color: ${equipo.color};">
            <div class="equipo-card-header">
                <div class="equipo-badge" style="background-color: ${equipo.color};">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="equipo-info">
                    <h4>${equipo.nombre}</h4>
                    <span class="equipo-categoria-badge">${equipo.categoria}</span>
                </div>
            </div>
            <div class="equipo-capitan">
                <i class="fas fa-star"></i>
                <span>Capitán: <strong>${equipo.capitan}</strong></span>
            </div>
            <div class="equipo-actions">
                <button class="btn btn-small btn-secondary" onclick="editarEquipo(${equipo.id})">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-small btn-danger" onclick="eliminarEquipo(${equipo.id})">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
            </div>
        </div>
    `).join('');
}

// Función para renderizar tabla de posiciones
function renderizarTablaPosiciones() {
    const tbody = document.getElementById('tablaPosiciones');
    if (!tbody) return;

    tbody.innerHTML = tablaPosiciones.map(equipo => `
        <tr>
            <td><strong>${equipo.pos}</strong></td>
            <td style="text-align: left;">${equipo.equipo}</td>
            <td>${equipo.pj}</td>
            <td>${equipo.pg}</td>
            <td>${equipo.pe}</td>
            <td>${equipo.pp}</td>
            <td><strong style="color: #10b981;">${equipo.pts}</strong></td>
        </tr>
    `).join('');
}

// Función para validar solo letras
function validarSoloLetras(input) {
    input.addEventListener('input', function(e) {
        // Eliminar cualquier carácter que no sea letra o espacio
        this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
    });
    
    // Prevenir pegar contenido con números
    input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pastedText = (e.clipboardData || window.clipboardData).getData('text');
        const cleanedText = pastedText.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
        document.execCommand('insertText', false, cleanedText);
    });
}

// Función para validar formulario
function validarFormularioEquipo() {
    const nombre = document.getElementById('nombreEquipo').value.trim();
    const categoria = document.getElementById('categoriaEquipo').value;
    const capitan = document.getElementById('capitan').value.trim();
    
    // Validar nombre del equipo
    if (nombre.length < 3) {
        mostrarMensaje('El nombre del equipo debe tener al menos 3 caracteres', 'error');
        return false;
    }
    
    if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(nombre)) {
        mostrarMensaje('El nombre del equipo solo puede contener letras', 'error');
        return false;
    }
    
    // Validar categoría
    if (!categoria) {
        mostrarMensaje('Por favor selecciona una categoría', 'error');
        return false;
    }
    
    // Validar capitán
    if (capitan.length < 5) {
        mostrarMensaje('El nombre del capitán debe tener al menos 5 caracteres', 'error');
        return false;
    }
    
    if (!/^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+$/.test(capitan)) {
        mostrarMensaje('El nombre del capitán solo puede contener letras', 'error');
        return false;
    }
    
    // Validar que el nombre del equipo no esté duplicado
    const nombreExiste = equipos.some(equipo => 
        equipo.nombre.toLowerCase() === nombre.toLowerCase()
    );
    
    if (nombreExiste) {
        mostrarMensaje('Ya existe un equipo con ese nombre', 'error');
        return false;
    }
    
    return true;
}

// Función para manejar el formulario de equipos
function manejarFormularioEquipo(e) {
    e.preventDefault();
    
    // Validar formulario
    if (!validarFormularioEquipo()) {
        return;
    }
    
    const nombre = document.getElementById('nombreEquipo').value.trim();
    const categoria = document.getElementById('categoriaEquipo').value;
    const capitan = document.getElementById('capitan').value.trim();
    const color = document.getElementById('colorEquipo').value;

    const nuevoEquipo = {
        id: equipos.length > 0 ? Math.max(...equipos.map(e => e.id)) + 1 : 1,
        nombre,
        categoria,
        capitan,
        color
    };

    equipos.push(nuevoEquipo);
    renderizarEquipos();
    
    // Limpiar formulario
    document.getElementById('equipoForm').reset();
    
    // Mostrar mensaje de éxito
    mostrarMensaje('¡Equipo creado exitosamente!', 'success');
    
    // Scroll suave a la lista de equipos
    setTimeout(() => {
        document.querySelector('.equipos-lista').scrollIntoView({ behavior: 'smooth' });
    }, 500);
}

// Función para editar equipo
function editarEquipo(id) {
    const equipo = equipos.find(e => e.id === id);
    if (!equipo) return;

    document.getElementById('nombreEquipo').value = equipo.nombre;
    document.getElementById('categoriaEquipo').value = equipo.categoria;
    document.getElementById('capitan').value = equipo.capitan;
    document.getElementById('colorEquipo').value = equipo.color;

    // Eliminar el equipo actual para que se reemplace al guardar
    equipos = equipos.filter(e => e.id !== id);
    
    // Scroll al formulario
    document.getElementById('equipos').scrollIntoView({ behavior: 'smooth' });
    
    mostrarMensaje('Modifica los datos y guarda los cambios', 'info');
}

// Función para eliminar equipo
function eliminarEquipo(id) {
    const equipo = equipos.find(e => e.id === id);
    if (!equipo) return;
    
    if (confirm(`¿Estás seguro de que deseas eliminar el equipo "${equipo.nombre}"?`)) {
        equipos = equipos.filter(e => e.id !== id);
        renderizarEquipos();
        mostrarMensaje('Equipo eliminado correctamente', 'success');
    }
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
    const colores = {
        success: '#10b981',
        error: '#ef4444',
        info: '#3b82f6'
    };
    
    const iconos = {
        success: '✓',
        error: '✕',
        info: 'ℹ'
    };
    
    const mensajeDiv = document.createElement('div');
    mensajeDiv.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: ${colores[tipo] || '#3b82f6'};
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        max-width: 400px;
    `;
    mensajeDiv.innerHTML = `<span style="font-size: 1.5rem; font-weight: bold;">${iconos[tipo] || 'ℹ'}</span> ${mensaje}`;
    
    document.body.appendChild(mensajeDiv);
    
    setTimeout(() => {
        mensajeDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => mensajeDiv.remove(), 300);
    }, 4000);
}

// Navegación suave
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Cerrar menú móvil si está abierto
            const navMenu = document.querySelector('.nav-menu');
            if (navMenu.classList.contains('active')) {
                navMenu.classList.remove('active');
            }
        }
    });
});

// Animación de aparición en scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '0';
            entry.target.style.transform = 'translateY(20px)';
            entry.target.style.transition = 'all 0.6s ease-out';
            
            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, 100);
            
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Renderizar contenido inicial
    renderizarPartidos();
    renderizarEquipos();
    renderizarTablaPosiciones();
    
    // Configurar formulario de equipos
    const form = document.getElementById('equipoForm');
    if (form) {
        form.addEventListener('submit', manejarFormularioEquipo);
    }
    
    // Aplicar validación solo letras a campos específicos
    const nombreEquipoInput = document.getElementById('nombreEquipo');
    const capitanInput = document.getElementById('capitan');
    
    if (nombreEquipoInput) {
        validarSoloLetras(nombreEquipoInput);
    }
    
    if (capitanInput) {
        validarSoloLetras(capitanInput);
    }
    
    // Validación en tiempo real visual
    const inputs = document.querySelectorAll('input[type="text"]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#ef4444';
            } else if (this.checkValidity()) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });
        
        input.addEventListener('focus', function() {
            this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
        });
    });
    
    // Observar elementos para animaciones
    const animatedElements = document.querySelectorAll('.partido-card, .info-card, .equipo-card');
    animatedElements.forEach(el => observer.observe(el));
    
    // Efecto parallax en el hero
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const heroDecoration = document.querySelector('.hero-decoration');
        if (heroDecoration) {
            heroDecoration.style.transform = `translateY(${scrolled * 0.5}px) rotate(${scrolled * 0.1}deg)`;
        }
    });
    
    // Mostrar mensaje de bienvenida
    setTimeout(() => {
        mostrarMensaje('¡Bienvenido a la Liga Escolar Digital!', 'info');
    }, 1000);
});

// Añadir estilos de animación dinámicamente
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);