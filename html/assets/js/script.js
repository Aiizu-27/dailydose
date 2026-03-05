document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;

    // ---------- Toggle Dark Mode ----------
    const toggleTema = document.getElementById("toggleTema");
    if(toggleTema){
        const solIcon = document.querySelector(".header-right .sol");
        const lunaIcon = document.querySelector(".header-right .luna");
        const circle = document.querySelector(".header-right .circle");

        toggleTema.addEventListener("change", () => {
            body.classList.toggle("dark-mode", toggleTema.checked);
            solIcon.style.opacity = toggleTema.checked ? "0" : "1";
            lunaIcon.style.opacity = toggleTema.checked ? "1" : "0";
            circle.style.transform = toggleTema.checked ? "translateX(26px)" : "translateX(0)";
        });
    }

    // ---------- Menú hamburguesa ----------
    const menuBtn = document.getElementById('menuBtn');
    const menuCerrar = document.getElementById('menuCerrar');
    const menu = document.querySelector('.menu-container'); // Ojo: clase corregida según tu CSS anterior

    if(menuBtn && menuCerrar && menu){
        menuBtn.addEventListener('click', () => menu.classList.add('open'));
        menuCerrar.addEventListener('click', () => menu.classList.remove('open'));
    }

    // ---------- Tabs Login / Registro ----------
    const tabLogin = document.getElementById("tabLogin");
    const tabRegistro = document.getElementById("tabRegistro");
    const formLogin = document.getElementById("formLogin");
    const formRegistro = document.getElementById("formRegistro");

    if(tabLogin && tabRegistro && formLogin && formRegistro){
        tabLogin.addEventListener("click", () => {
            tabLogin.classList.add("active");
            tabRegistro.classList.remove("active");
            formLogin.style.display = "block";
            formRegistro.style.display = "none";
        });
        tabRegistro.addEventListener("click", () => {
            tabRegistro.classList.add("active");
            tabLogin.classList.remove("active");
            formRegistro.style.display = "block";
            formLogin.style.display = "none";
        });
    }

    // ---------- Mostrar / Ocultar contraseña ----------
    function togglePassword(inputId, iconId){
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if(!input || !icon) return;

        const ojoAbierto = icon.querySelectorAll(".ojo-abierto");
        const ojoCerrado = icon.querySelectorAll(".ojo-cerrado");
        // Inicializar estado visual
        if(input.type === "password"){
             ojoCerrado.forEach(p => p.style.display = "none");
             ojoAbierto.forEach(p => p.style.display = "block");
        }

        icon.addEventListener("click", () => {
            const tipo = input.type === "password" ? "text" : "password";
            input.type = tipo;
            
            if(tipo === "text"){
                ojoAbierto.forEach(p => p.style.display = "none");
                ojoCerrado.forEach(p => p.style.display = "block");
            } else {
                ojoAbierto.forEach(p => p.style.display = "block");
                ojoCerrado.forEach(p => p.style.display = "none");
            }
        });
    }

    togglePassword("claveLogin", "iconoOjoLogin");
    togglePassword("claveRegistro", "iconoOjoRegistro");

    // ==========================================
    // LÓGICA AJAX (Fetch) - CORREGIDA
    // ==========================================

    // LOGIN
    document.getElementById('formLogin').addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);

        fetch('actions/auth_login.php', { method:'POST', body: formData })
        .then(res => res.json()) // ahora esperamos JSON
        .then(data => {
            if(data.status === "login_ok"){
                // Redirigir según rol
                if(data.rol === "cliente"){
                    window.location.href = "panel/dashboard_cliente.php";
                } else if(data.rol === "trabajador"){
                    window.location.href = "panel/dashboard_trabajador.php";
                } else if(data.rol === "admin"){
                    window.location.href = "panel/dashboard_admin.php";
                } else {
                    alert("Rol desconocido: " + data.rol);
                }
            } else if(data.status === "contraseña_incorrecta"){
                alert("Contraseña incorrecta");
            } else if(data.status === "usuario_no_encontrado"){
                alert("Usuario no encontrado");
            } else {
                alert("Error desconocido: " + JSON.stringify(data));
            }
        })
        .catch(err => console.error("Error en fetch:", err));
    });

    // REGISTRO
    if(formRegistro) { // Solo ejecutar si el formulario existe
        formRegistro.addEventListener('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);

            // RUTA CORREGIDA: 'actions/auth_registro.php'
            fetch('actions/auth_registro.php', { method:'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                const respuesta = data.trim();

                if(respuesta === "registro_ok"){
                    alert("Registro exitoso. Ya puedes iniciar sesión.");
                    // Si existe el tabLogin, hacemos click para cambiar de pestaña
                    if(document.getElementById('tabLogin')) {
                        document.getElementById('tabLogin').click();
                    }
                    this.reset();
                } else if(respuesta === "correo_existente"){
                    alert("Este correo ya está registrado.");
                } else {
                    alert("Error: " + respuesta);
                }
            })
            .catch(err => console.error("Error en fetch:", err));
        });
    }
});

