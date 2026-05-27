<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <style>
        #toast {
            position: fixed;
            top: 1.2rem;
            right: 1.2rem;
            z-index: 9999;
            padding: .85rem 1.25rem;
            border-radius: .6rem;
            font-size: .9rem;
            font-weight: 500;
            box-shadow: 0 6px 24px rgba(0,0,0,.15);
            opacity: 0;
            transform: translateX(110%);
            transition: opacity .3s ease, transform .35s ease;
            max-width: 320px;
        }
        #toast.show { opacity: 1; transform: translateX(0); }
    </style>
</head>

<body class="min-h-screen bg-red-100 flex items-center justify-center">

    <div id="toast"></div>

    <div class="bg-white p-8 shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-black-700 mb-6">Crear Registro</h2>

        <form id="miForm" class="space-y-4">

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Correo electrónico</label>
                <input
                    type="email"
                    name="cor"
                    placeholder="EmailEjemplo@correo.com"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                <input
                    type="text"
                    name="nom"
                    placeholder="Introduce tu Nombre"
                    required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>

            <input
                type="submit"
                value="Registrarse"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded cursor-pointer transition duration-200"
            >
        </form>
    </div>
      
      
    <script>
    
    $(document).ready(function () {

        function toast(msg, tipo) {
            const colores = {
                success: 'background:#d1fae5; color:#065f46; border-left:4px solid #10b981',
                warn:    'background:#fef3c7; color:#78350f; border-left:4px solid #f59e0b',
                error:   'background:#fee2e2; color:#7f1d1d; border-left:4px solid #ef4444'
            };

            // estilos + texto al div#toast
            $('#toast').css('cssText', colores[tipo]).text(msg);

            
            setTimeout(() => $('#toast').addClass('show'), 10);

            // A los 4 segundos, ocultamos el toast
            setTimeout(() => $('#toast').removeClass('show'), 4000);
        }

          $('#miForm').on('submit', function (e) {

            
            e.preventDefault();

            //  $.ajax envía la petición al servidor en segundo plano
            $.ajax({
                url:      'GuardaUsuarios.php',
                type:     'POST',                
                data:     $(this).serialize(),   
                dataType: 'json',               

                
                success: function (res) {
                
                    if (res.status === 'success') {
                        toast('✅ Registrado. Contraseña: ' + res.password, 'success');
                        $('#miForm').trigger('reset'); // limpia los campos
                    } else if (res.status === 'warn') {
                        toast('⚠️ ' + res.mensaje, 'warn');
                    } else {
                        toast('❌ ' + res.mensaje, 'error');
                    }
                },

                    error: function () {
                    toast('❌ Error de conexión con el servidor.', 'error');
                }
            });
        });

    });
    </script>

</body>
</html>