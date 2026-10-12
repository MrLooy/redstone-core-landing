/* ============================================================================
   REDSTONE CORE · main.js
   JavaScript puro (ES6) sin dependencias.
   Conserva la validación original del formulario y añade mejoras no intrusivas:
   menú hamburguesa, scroll suave, scrollspy, revelado al hacer scroll y
   botón "volver arriba". Cada mejora comprueba que sus elementos existan,
   de modo que nunca lanza errores si algún nodo falta en el DOM.
   ============================================================================ */

document.addEventListener('DOMContentLoaded', () => {
    'use strict';

    /* 1) VALIDACIÓN DEL FORMULARIO (misma lógica que se usaba antes) */
    const form = document.querySelector('form');

    if (form) {
        form.addEventListener('submit', (e) => {
            const nombre = document.getElementById('nombre');
            const apePat = document.getElementById('ape_pat');
            const correo = document.getElementById('correo');
            const mensaje = document.getElementById('mensaje');

            // Si algún campo obligatorio no existe, dejamos pasar el envío.
            if (nombre && apePat && correo && mensaje) {
                const vNombre = nombre.value.trim();
                const vApePat = apePat.value.trim();
                const vCorreo = correo.value.trim();
                const vMensaje = mensaje.value.trim();

                if (!vNombre || !vApePat || !vCorreo || !vMensaje) {
                    e.preventDefault();
                    alert('Por favor, completa todos los campos obligatorios antes de enviar.');
                } else {
                    // Datos obligatorios presentes: activar estado "Enviando..."
                    activarEnviando(form);
                }
            }
        });
    }

    /* 2. MENÚ HAMBURGUESA (mobile) */
    const menuToggle = document.getElementById('menuToggle');
    const nav = document.querySelector('.nav');

    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            const abierto = document.body.classList.toggle('menu-open');
            menuToggle.setAttribute('aria-expanded', String(abierto));
        });

        // Cerrar el menú al hacer clic en cualquier enlace, sin bloquear el
        // honeypot ni los enlaces externos (formato https:// o mailto:).
        nav.querySelectorAll('a').forEach((enlace) => {
            enlace.addEventListener('click', () => {
                document.body.classList.remove('menu-open');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    /* 3. SCROLL SUAVE PARA ANCLAS (#inicio, #servicios, #nosotros, #contacto) */
    const header = document.querySelector('.header');
    // El offset se recalcula al compactarse el header (ver módulo 7).
    let offset = header ? header.offsetHeight + 14 : 14;

    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const hash = anchor.getAttribute('href');
            if (!hash || hash.length < 2) return; // descarta "#" suelto
            const destino = document.querySelector(hash);
            if (!destino) return;

            e.preventDefault();
            const top = destino.getBoundingClientRect().top + window.pageYOffset - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });

    /* 4. SCROLLSPY: resaltar la sección activa en la navegación */
    const secciones = document.querySelectorAll('section[id]');
    const enlacesNav = document.querySelectorAll('.nav a[href^="#"]');

    if (secciones.length && enlacesNav.length && 'IntersectionObserver' in window) {
        const spy = new IntersectionObserver((entradas) => {
            entradas.forEach((entrada) => {
                if (!entrada.isIntersecting) return;
                const idActual = '#' + entrada.target.id;
                enlacesNav.forEach((enlace) => {
                    enlace.classList.toggle('active', enlace.getAttribute('href') === idActual);
                });
                // Reubicar el rail deslizante sobre el enlace activo
                actualizarNavTab();
            });
        }, { rootMargin: '-35% 0px -60% 0px' });

        secciones.forEach((seccion) => spy.observe(seccion));
    }

    /* 5. REVELAR ELEMENTOS AL HACER SCROLL (.reveal, .reveal-left, .reveal-right) */
    const revealItems = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');

    if (revealItems.length) {
        if ('IntersectionObserver' in window) {
            const observador = new IntersectionObserver((entradas, obs) => {
                entradas.forEach((entrada) => {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add('visible');
                        obs.unobserve(entrada.target); // solo una vez por elemento
                    }
                });
            }, { threshold: 0.12 });

            revealItems.forEach((el) => observador.observe(el));
        } else {
            // Sin soporte de IntersectionObserver: mostrar todo de inmediato.
            revealItems.forEach((el) => el.classList.add('visible'));
        }
    }

    /* 6. BOTÓN "VOLVER ARRIBA" */
    const botonArriba = document.getElementById('backToTop');

    if (botonArriba) {
        const alScroll = () => {
            botonArriba.classList.toggle('show', window.pageYOffset > 520);
        };

        window.addEventListener('scroll', alScroll, { passive: true });
        alScroll(); // estado inicial

        botonArriba.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* 7. HEADER COMPACTO + BARRA DE PROGRESO DE SCROLL */
    const encabezado = document.querySelector('.header');
    let barraProgreso = null;

    if (encabezado) {
        barraProgreso = document.createElement('div');
        barraProgreso.className = 'scroll-progress';
        barraProgreso.setAttribute('aria-hidden', 'true');
        encabezado.appendChild(barraProgreso);
        document.body.classList.add('has-progress');
    }

    let scrollTick = false;

    function actualizarEstadoHeader() {
        const y = window.pageYOffset || document.documentElement.scrollTop;

        if (encabezado) {
            encabezado.classList.toggle('header-scrolled', y > 24);
            // Mantener el scroll suave alineado con el alto real del header
            offset = encabezado.offsetHeight + 14;
        }

        if (barraProgreso) {
            const altoDoc = document.documentElement.scrollHeight - window.innerHeight;
            const avance = altoDoc > 0 ? Math.min(1, Math.max(0, y / altoDoc)) : 0;
            barraProgreso.style.transform = 'scaleX(' + avance.toFixed(4) + ')';
        }
    }

    function alDesplazarse() {
        if (scrollTick) return; // throttling con requestAnimationFrame
        scrollTick = true;
        window.requestAnimationFrame(() => {
            actualizarEstadoHeader();
            scrollTick = false;
        });
    }

    window.addEventListener('scroll', alDesplazarse, { passive: true });
    actualizarEstadoHeader(); // estado inicial

    /* 8. RAIL ACTIVO DESLIZANTE EN LA NAVEGACIÓN */
    const navPrincipal = document.querySelector('.nav');
    let tabActivo = null;

    function actualizarNavTab() {
        if (!navPrincipal) return;
        const escritorio = window.matchMedia('(min-width: 900px)').matches;

        if (!tabActivo) {
            tabActivo = document.createElement('span');
            tabActivo.className = 'nav-active-tab';
            tabActivo.setAttribute('aria-hidden', 'true');
            navPrincipal.appendChild(tabActivo);
        }

        const activo = navPrincipal.querySelector('a.active[href^="#"]');

        if (activo && escritorio) {
            tabActivo.style.left = activo.offsetLeft + 'px';
            tabActivo.style.width = activo.offsetWidth + 'px';
            navPrincipal.classList.add('has-active');
        } else {
            navPrincipal.classList.remove('has-active');
        }
    }

    window.addEventListener('resize', actualizarNavTab, { passive: true });
    actualizarNavTab(); // estado inicial

    /* 9. RIPPLE EN BOTONES (micro-interacción al clic, CSS + JS ligero) */
    const menosMovimiento = () =>
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.btn-primary, .btn-secondary, button[type="submit"]').forEach((boton) => {
        boton.addEventListener('click', (e) => {
            const rect = boton.getBoundingClientRect();
            const diametro = Math.max(rect.width, rect.height) * 1.15;
            const onda = document.createElement('span');
            onda.className = 'btn-ripple';
            onda.style.width = onda.style.height = diametro + 'px';
            onda.style.left = (e.clientX - rect.left - diametro / 2) + 'px';
            onda.style.top = (e.clientY - rect.top - diametro / 2) + 'px';
            boton.appendChild(onda);
            onda.addEventListener('animationend', () => onda.remove(), { once: true });
        });
    });

    /* 10. FORMULARIO: ESTADO "ENVIANDO..." (no toca PHP ni el honeypot) */
    function activarEnviando(frm) {
        if (!frm) return;
        const btn = frm.querySelector('button[type="submit"]');
        if (!btn || btn.classList.contains('is-sending')) return;

        btn.classList.add('is-sending');
        btn.setAttribute('aria-busy', 'true');
        btn.disabled = true;

        const contenedor = document.createElement('span');
        contenedor.className = 'btn-sending-content';
        const spinner = document.createElement('span');
        spinner.className = 'btn-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        const etiqueta = document.createElement('span');
        etiqueta.textContent = 'Enviando...';
        contenedor.append(spinner, etiqueta);

        btn.replaceChildren(contenedor);
    }

    /* 11. PARALLAX LIGERO EN EL HERO (solo escritorio y sin menos-movimiento) */
    const ilustracionHero = document.querySelector('.hero-illustration');
    const pantallaEscritorio = () => window.matchMedia('(min-width: 900px)').matches;

    if (ilustracionHero && pantallaEscritorio() && !menosMovimiento()) {
        document.body.classList.add('js-parallax');
        let parTick = false;
        const moverParallax = () => {
            if (parTick) return;
            parTick = true;
            window.requestAnimationFrame(() => {
                const y = window.pageYOffset || 0;
                if (y < window.innerHeight * 1.4) {
                    ilustracionHero.style.transform =
                        'translate3d(0, ' + (y * 0.07).toFixed(2) + 'px, 0)';
                }
                parTick = false;
            });
        };
        window.addEventListener('scroll', moverParallax, { passive: true });
        moverParallax(); // estado inicial
    }

    /* 12. TEMA CLARO / OSCURO (persistente) */
    const raiz = document.documentElement;
    const botonTema = document.getElementById('themeToggle');
    const temaGuardado = (() => {
        try { return localStorage.getItem('rc-tema'); } catch (e) { return null; }
    })();

    if (temaGuardado === 'light' || temaGuardado === 'dark') {
        raiz.setAttribute('data-theme', temaGuardado);
    }
    if (botonTema) {
        botonTema.setAttribute('aria-pressed', raiz.getAttribute('data-theme') === 'light');
        const animarBoton = (btn) => {
            if (menosMovimiento()) return;
            btn.classList.remove('is-toggling');
            void btn.offsetWidth; // reinicia la animación
            btn.classList.add('is-toggling');
        };
        const darTema = (tema) => {
            raiz.setAttribute('data-theme', tema);
            botonTema.setAttribute('aria-pressed', tema === 'light');
            try { localStorage.setItem('rc-tema', tema); } catch (e) {}
        };
        botonTema.addEventListener('click', () => {
            const actual = raiz.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            animarBoton(botonTema);
            const aplicar = () => darTema(actual);
            if (document.startViewTransition && !menosMovimiento()) {
                try {
                    document.startViewTransition(aplicar);
                } catch (e) {
                    aplicar();
                }
            } else {
                aplicar();
            }
        });
    }

    /* 13. VISTA MÓVIL / PC (marco de previsualización) */
    const botonDispositivo = document.getElementById('deviceToggle');
    const overlayDispositivo = document.getElementById('deviceOverlay');
    const botonCerrarDispositivo = document.getElementById('deviceClose');

    // Si esta página se está mostrando DENTRO del marco de vista previa,
    // se ocultan las herramientas para evitar botones anidados.
    if (window.self !== window.top) {
        const herramientas = document.querySelectorAll('.header-tools');
        herramientas.forEach((h) => { h.style.display = 'none'; });
    }

    if (botonDispositivo && overlayDispositivo) {
        const animarBoton = (btn) => {
            if (menosMovimiento()) return;
            btn.classList.remove('is-toggling');
            void btn.offsetWidth; // reinicia la animación
            btn.classList.add('is-toggling');
        };
        botonDispositivo.addEventListener('click', () => {
            overlayDispositivo.hidden = !overlayDispositivo.hidden;
            document.body.classList.toggle('has-device-overlay', !overlayDispositivo.hidden);
            animarBoton(botonDispositivo);
        });
        if (botonCerrarDispositivo) {
            botonCerrarDispositivo.addEventListener('click', () => {
                overlayDispositivo.hidden = true;
                document.body.classList.remove('has-device-overlay');
            });
        }
        overlayDispositivo.addEventListener('click', (e) => {
            if (e.target === overlayDispositivo) {
                overlayDispositivo.hidden = true;
                document.body.classList.remove('has-device-overlay');
            }
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !overlayDispositivo.hidden) {
                overlayDispositivo.hidden = true;
                document.body.classList.remove('has-device-overlay');
            }
        });
    }

    // Efecto sutil en consola para confirmar carga correcta
    console.log('RedstoneCore JS cargado correctamente.');
});