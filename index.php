<?php
// Variables de contacto globales
$whatsapp_numero = "+51 900 000 000";
$whatsapp_url    = "https://wa.me/519000000000";
$correo_directo  = "contacto@redstonecore.example";
$instagram_url   = "#"; // pendiente de crear
$facebook_url    = "#"; // pendiente de crear

include 'conexion.php';
$mensaje_estado = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Honeypot anti-spam: campo invisible para humanos, que los bots suelen llenar.
    // Si viene con contenido, se descarta el envio en silencio (respuesta identica
    // a un envio exitoso, para no revelar al bot que fue detectado).
    if (!empty($_POST['website'])) {
        $mensaje_estado = "<p class='alert success'>¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.</p>";
    } else {
        $nombre  = trim($_POST['nombre']);
        $ape_pat = trim($_POST['ape_pat']);
        $ape_mat = trim($_POST['ape_mat']);
        $correo  = trim($_POST['correo']);
        $empresa = trim($_POST['empresa']);
        $mensaje = trim($_POST['mensaje']);

        if (!empty($nombre) && !empty($ape_pat) && !empty($correo) && !empty($mensaje)) {
            try {
                $sql = "INSERT INTO contacto (nombre, ape_pat, ape_mat, correo, empresa, mensaje)
                        VALUES (:nombre, :ape_pat, :ape_mat, :correo, :empresa, :mensaje)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'nombre'  => $nombre,
                    'ape_pat' => $ape_pat,
                    'ape_mat' => $ape_mat !== '' ? $ape_mat : null,
                    'correo'  => $correo,
                    'empresa' => $empresa !== '' ? $empresa : null,
                    'mensaje' => $mensaje
                ]);
                $mensaje_estado = "<p class='alert success'>¡Mensaje enviado con éxito! Nos pondremos en contacto pronto.</p>";
            } catch (PDOException $e) {
                error_log('Error al insertar contacto: ' . $e->getMessage());
                $mensaje_estado = "<p class='alert error'>Error al registrar el mensaje.</p>";
            }
        } else {
            $mensaje_estado = "<p class='alert warning'>Por favor completa los campos obligatorios.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Redstone Core: ingeniería de software a medida para empresas, colegios, clínicas, MYPES y organizaciones en Perú. Sistemas inteligentes y automatización de procesos.">
    <meta name="author" content="Redstone Core Engineering">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#ef4444">
    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="es_PE">
    <meta property="og:title" content="Redstone Core | Sistemas Inteligentes &amp; Software a Medida">
    <meta property="og:description" content="Ingeniería de software y automatización de procesos para empresas, colegios, clínicas y MYPES en Perú.">
    <meta property="og:url" content="https://redstonecore.example/">
    <meta name="twitter:card" content="summary">
    <title>Redstone Core | Sistemas Inteligentes &amp; Software a Medida</title>
    <!-- Favicon (SVG embebido, sin archivos externos) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='13' fill='%23ef4444'/%3E%3Crect x='7' y='7' width='50' height='50' rx='9' fill='none' stroke='%23060a12' stroke-width='3' opacity='0.4'/%3E%3Ctext x='32' y='44' font-family='Arial Black, Arial' font-size='34' font-weight='900' text-anchor='middle' fill='%23060a12'%3ER%3C/text%3E%3C/svg%3E">
    <!-- Tipografías (Google Fonts, opcional y ligera) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Space+Grotesk:wght@500;600;700&amp;family=JetBrains+Mono:wght@400;500;700&amp;display=swap" rel="stylesheet">
    <!-- Iconos de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=3">
    <!-- Marcador para activar animaciones solo con JS disponible -->
    <script>document.documentElement.classList.add('js');</script>
</head>
<body>

    <!-- Enlace de accesibilidad: saltar directamente al hero -->
    <a class="skip-link" href="#inicio">Saltar al contenido</a>

    <!-- NAVBAR -->
    <header class="header">
        <a href="#inicio" class="logo" aria-label="Redstone Core, ir al inicio">
            <h1>REDSTONE<span>CORE</span></h1>
        </a>
        <button type="button" class="menu-toggle" id="menuToggle" aria-label="Abrir menú" aria-expanded="false" aria-controls="mainNav">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="header-actions">
            <nav class="nav" id="mainNav" aria-label="Navegación principal">
                <a href="#inicio">Inicio</a>
                <a href="#servicios">Servicios</a>
                <a href="#nosotros">¿Por qué elegirnos?</a>
                <a href="#contacto">Contacto</a>
                <a href="<?php echo $whatsapp_url; ?>" target="_blank" rel="noopener" class="nav-whatsapp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php echo $whatsapp_numero; ?></a>
            </nav>
            <!-- Herramientas de visualización: tema claro/oscuro y vista móvil/PC -->
            <div class="header-tools" role="group" aria-label="Herramientas de visualización">
                <button type="button" class="view-tool" id="themeToggle" title="Cambiar tema claro/oscuro" aria-label="Cambiar tema claro/oscuro" aria-pressed="false">
                    <i class="fa-solid fa-sun" aria-hidden="true"></i>
                    <i class="fa-solid fa-moon" aria-hidden="true"></i>
                </button>
                <button type="button" class="view-tool" id="deviceToggle" title="Vista móvil / PC" aria-label="Cambiar vista móvil o PC" aria-pressed="false">
                    <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i>
                    <i class="fa-solid fa-desktop" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- HERO SECTION CON ILUSTRACIÓN -->
    <section id="inicio" class="hero-container">
        <div class="hero-text">
            <p class="hero-kicker">&lt;/ redstone core &gt;&nbsp;circuitos &middot; datos &middot; automatización</p>
            <h2>Desarrollamos <span>sistemas inteligentes</span> para impulsar el crecimiento de tu negocio</h2>
            <p>Creamos software personalizado para empresas, colegios, clínicas, MYPES y organizaciones que desean automatizar sus procesos.</p>
            <div class="hero-buttons">
                <a href="#contacto" class="btn-primary">Solicitar Cotización</a>
                <a href="#servicios" class="btn-secondary">Ver nuestros servicios</a>
            </div>
        </div>
        <div class="hero-illustration">
            <div class="tech-card-mockup">
                <!-- Diagrama de circuito "redstone" en SVG puro -->
                <svg class="circuit-diagram" viewBox="0 0 360 240" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                    <defs>
                        <linearGradient id="rcTrace" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0" stop-color="#ef4444"/>
                            <stop offset="1" stop-color="#f59e0b"/>
                        </linearGradient>
                        <radialGradient id="rcCore" cx="50%" cy="45%" r="80%">
                            <stop offset="0" stop-color="#fbbf24"/>
                            <stop offset="45%" stop-color="#ef4444"/>
                            <stop offset="100%" stop-color="#991b1b"/>
                        </radialGradient>
                        <filter id="coreBlur" x="-60%" y="-60%" width="220%" height="220%">
                            <feGaussianBlur stdDeviation="14"/>
                        </filter>
                    </defs>

                    <!-- Pistas del circuito -->
                    <g stroke-linejoin="round" stroke-linecap="round">
                        <path class="trace" d="M18 30 H118 L136 120 H168"/>
                        <path class="trace t2" d="M342 50 H312 L282 80 H190"/>
                        <path class="trace t3" d="M18 185 H100 L128 155 H215"/>
                        <path class="trace t4" d="M342 200 H308 L254 160 H225"/>
                        <path class="trace t5" d="M20 120 H98 L126 165 H150"/>
                        <path class="trace t2" d="M342 30 H318 L296 60 H262"/>
                    </g>

                    <!-- Nodos: lingotes de polvo de redstone -->
                    <circle class="node" cx="168" cy="120" r="5"/>
                    <circle class="node" cx="190" cy="80" r="5"/>
                    <circle class="node" cx="215" cy="155" r="5"/>
                    <circle class="node" cx="262" cy="60" r="5"/>
                    <circle class="node" cx="150" cy="165" r="5"/>
                    <circle class="node" cx="98" cy="66" r="5"/>

                    <!-- Halo y núcleo redstone (corazón energizado) -->
                    <ellipse class="halo" cx="180" cy="120" rx="76" ry="60" fill="#ef4444" filter="url(#coreBlur)"/>
                    <rect class="core" x="152" y="92" width="56" height="56" rx="10" fill="url(#rcCore)"/>
                    <rect x="152" y="92" width="56" height="56" rx="10" fill="none" stroke="rgba(255,255,255,0.45)" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M174 100 v16 M180 104 v8 M170 106 v8" stroke="rgba(255,255,255,0.85)" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="180" cy="110" r="2.5" fill="#fff" opacity="0.9"/>
                </svg>

                <div class="tech-icons">
                    <i class="fa-solid fa-chart-line" aria-hidden="true"></i>
                    <i class="fa-solid fa-laptop-code" aria-hidden="true"></i>
                    <i class="fa-solid fa-mobile-screen-button" aria-hidden="true"></i>
                </div>
                <p>Dashboard &amp; Automation</p>
            </div>
        </div>
    </section>

    <!-- ¿QUÉ HACEMOS? (SERVICIOS) -->
    <section id="servicios" class="servicios-section">
        <div class="section-title reveal">
            <h2>Soluciones digitales adaptadas a cada organización</h2>
            <p>Desarrollamos tecnología robusta pensada en la necesidad específica de tu sector.</p>
        </div>
        <div class="cards-grid">
            <div class="card reveal">
                <div class="card-icon"><i class="fa-solid fa-briefcase" aria-hidden="true"></i></div>
                <h3>Empresas</h3>
                <p>Sistema de ventas, inventario, RRHH y facturación electrónica.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon"><i class="fa-solid fa-school" aria-hidden="true"></i></div>
                <h3>Colegios Privados</h3>
                <p>Notas, matrícula, asistencia, pagos y plataforma para docentes.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon"><i class="fa-solid fa-store" aria-hidden="true"></i></div>
                <h3>MYPES</h3>
                <p>Control de ventas, gestión de clientes y almacén rápido.</p>
            </div>
            <div class="card reveal">
                <div class="card-icon"><i class="fa-solid fa-hospital-user" aria-hidden="true"></i></div>
                <h3>Clínicas</h3>
                <p>Gestión de pacientes, historias clínicas y control de citas.</p>
            </div>
            <div class="card card-wide reveal">
                <div class="card-icon"><i class="fa-solid fa-gears" aria-hidden="true"></i></div>
                <h3>Sistemas Personalizados</h3>
                <p>Desarrollamos exactamente la plataforma o software que necesita tu empresa desde cero.</p>
            </div>
        </div>
    </section>

    <!-- POR QUÉ ELEGIRNOS -->
    <section id="nosotros" class="features-section">
        <div class="section-title reveal">
            <h2>¿Por qué elegir Redstone Core?</h2>
        </div>
        <div class="features-grid">
            <div class="feature-item reveal">
                <i class="fa-solid fa-code" aria-hidden="true"></i>
                <h4>Desarrollo a medida</h4>
                <p>No usamos plantillas genéricas. Construimos código limpio adaptado a tu flujo.</p>
            </div>
            <div class="feature-item reveal">
                <i class="fa-solid fa-microchip" aria-hidden="true"></i>
                <h4>Tecnología moderna</h4>
                <p>Aplicaciones rápidas, eficientes y con altos estándares de ingeniería.</p>
            </div>
            <div class="feature-item reveal">
                <i class="fa-solid fa-headset" aria-hidden="true"></i>
                <h4>Soporte técnico</h4>
                <p>Acompañamiento y mantenimiento constante después de la entrega.</p>
            </div>
            <div class="feature-item reveal">
                <i class="fa-solid fa-shield-halved" aria-hidden="true"></i>
                <h4>Seguridad</h4>
                <p>Protección de la información y respaldo de bases de datos.</p>
            </div>
            <div class="feature-item reveal">
                <i class="fa-solid fa-chart-line-up" aria-hidden="true"></i>
                <h4>Escalabilidad</h4>
                <p>Sistemas modulares preparados para crecer junto con tu negocio.</p>
            </div>
        </div>
    </section>

    <!-- FORMULARIO DE CONTACTO -->
    <section id="contacto" class="container reveal">
        <div class="contacto-form">
            <h3>Contáctanos</h3>
            <p>Cuéntanos sobre tu proyecto y te ayudaremos a automatizarlo.</p>

            <?php echo $mensaje_estado; ?>

            <form action="index.php#contacto" method="POST">
                <!-- Honeypot anti-spam: invisible para humanos, los bots suelen llenarlo -->
                <div class="hp-field" aria-hidden="true">
                    <label for="website">No llenar este campo</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nombre">Nombre <span class="label-req">*</span></label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej. Juan">
                    </div>
                    <div class="form-group">
                        <label for="ape_pat">Apellido Paterno <span class="label-req">*</span></label>
                        <input type="text" id="ape_pat" name="ape_pat" required placeholder="Ej. Pérez">
                    </div>
                    <div class="form-group">
                        <label for="ape_mat">Apellido Materno (Opcional)</label>
                        <input type="text" id="ape_mat" name="ape_mat" placeholder="Ej. García">
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo Electrónico <span class="label-req">*</span></label>
                        <input type="email" id="correo" name="correo" required placeholder="correo@ejemplo.com">
                    </div>
                </div>
                <div class="form-group">
                    <label for="empresa">Empresa o Colegio (Opcional)</label>
                    <input type="text" id="empresa" name="empresa" placeholder="Nombre de tu negocio">
                </div>
                <div class="form-group">
                    <label for="mensaje">¿Qué proceso deseas automatizar? <span class="label-req">*</span></label>
                    <textarea id="mensaje" name="mensaje" rows="4" required placeholder="Describe brevemente tus necesidades..."></textarea>
                </div>
                <button type="submit">
                    Enviar Mensaje <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </section>

    <!-- FOOTER COMPLETO CON VARIABLES -->
    <footer class="footer">
        <div class="footer-grid">
            <div class="footer-col">
                <h2>REDSTONE<span>CORE</span></h2>
                <p>Ingeniería de software, automatización de procesos y desarrollo a medida.</p>
            </div>
            <div class="footer-col">
                <h4>Contacto Directo</h4>
                <p><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Perú</p>
                <p><i class="fa-solid fa-envelope" aria-hidden="true"></i> <?php echo $correo_directo; ?></p>
                <p><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> <?php echo $whatsapp_numero; ?></p>
            </div>
            <div class="footer-col">
                <h4>Redes Sociales</h4>
                <div class="social-links">
                    <a href="<?php echo $whatsapp_url; ?>" target="_blank" rel="noopener"><i aria-hidden="true" class="fa-brands fa-whatsapp"></i> WhatsApp Directo</a>
                    <a href="<?php echo $facebook_url; ?>" target="_blank" rel="noopener"><i aria-hidden="true" class="fa-brands fa-facebook"></i> Facebook (Próximamente)</a>
                    <a href="<?php echo $instagram_url; ?>" target="_blank" rel="noopener"><i aria-hidden="true" class="fa-brands fa-instagram"></i> Instagram (Próximamente)</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Redstone Core Engineering. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Botón "volver arriba" -->
    <button type="button" class="back-to-top" id="backToTop" aria-label="Volver arriba" title="Volver arriba">
        <i class="fa-solid fa-chevron-up" aria-hidden="true"></i>
    </button>

    <!-- Herramientas de visualización: tema claro/oscuro y vista móvil/PC -->

    <!-- Marco de vista previa móvil (se omite al cargar dentro del propio iframe) -->
    <?php if (!isset($_GET['preview'])): ?>
    <div class="device-overlay" id="deviceOverlay" hidden>
        <div class="device-frame">
            <div class="device-bar">
                <span class="device-title">Vista móvil · Redstone Core</span>
                <button type="button" id="deviceClose" class="device-close" aria-label="Cerrar vista móvil">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <div class="device-screen">
                <iframe id="deviceFrame" src="index.php?preview=1" title="Vista previa móvil del sitio"></iframe>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>