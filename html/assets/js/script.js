document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;


const toggleTema = document.getElementById("toggleTema");
if(toggleTema){
    const solIcon = document.querySelector(".header-right .sol");
    const lunaIcon = document.querySelector(".header-right .luna");
    const circle = document.querySelector(".header-right .circle");

    const temaGuardado = localStorage.getItem("tema_daily_dose");
    
    if (temaGuardado === "dark") {
        body.classList.add("dark-mode");
        toggleTema.checked = true;
        if(solIcon) solIcon.style.opacity = "0";
        if(lunaIcon) lunaIcon.style.opacity = "1";
        if(circle) circle.style.transform = "translateX(26px)";
    }

    toggleTema.addEventListener("change", () => {
        if (toggleTema.checked) {
            body.classList.add("dark-mode");
            localStorage.setItem("tema_daily_dose", "dark");
            
            if(solIcon) solIcon.style.opacity = "0";
            if(lunaIcon) lunaIcon.style.opacity = "1";
            if(circle) circle.style.transform = "translateX(26px)";
        } else {
            body.classList.remove("dark-mode");
            localStorage.setItem("tema_daily_dose", "light");
            if(solIcon) solIcon.style.opacity = "1";
            if(lunaIcon) lunaIcon.style.opacity = "0";
            if(circle) circle.style.transform = "translateX(0)";
        }
    });
}

    const menuBtn = document.getElementById('menuBtn');
    const menuCerrar = document.getElementById('menuCerrar');
    const menu = document.querySelector('.menu-container');

    if(menuBtn && menuCerrar && menu){
        menuBtn.addEventListener('click', () => menu.classList.add('open'));
        menuCerrar.addEventListener('click', () => menu.classList.remove('open'));
    }

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


    document.getElementById('formLogin').addEventListener('submit', function(e){
        e.preventDefault();
        const formData = new FormData(this);

        fetch('/actions/auth_login.php', { method:'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if(data.status === "login_ok"){
                if(data.rol === "CLIENTE"){
                    window.location.href = "/panel/dashboard_cliente.php";
                } else if(data.rol === "EMPLEADO"){
                    window.location.href = "/panel/dashboard_trabajador.php";
                } else if(data.rol === "ADMIN"){
                    window.location.href = "/panel/dashboard_admin.php";
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


    if(formRegistro) { 
        formRegistro.addEventListener('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);

            fetch('/actions/auth_registro.php', { method:'POST', body: formData })
            .then(res => res.text())
            .then(data => {
                const respuesta = data.trim();

                if(respuesta === "registro_ok"){
                    alert("Registro exitoso. Ya puedes iniciar sesión.");
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

