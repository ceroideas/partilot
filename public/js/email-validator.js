/**
 * Validador de emails con verificación AJAX contextual
 * Verifica si el email ya está en uso según el contexto (administración, entidad, usuario, vendedor)
 */

(function() {
    'use strict';

    /**
     * Validar formato de email
     */
    function isValidEmailFormat(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Inicializar validación de email con verificación AJAX
     * @param {string} inputId - ID del input de email
     * @param {object} options - Opciones de configuración
     * @param {string} options.context - Contexto: 'administration', 'entity', 'user', 'seller'
     * @param {number|null} options.excludeId - ID a excluir de la verificación (para edición)
     * @param {boolean} options.showMessage - Mostrar mensajes de validación
     * @param {function} options.onValid - Callback cuando el email es válido
     * @param {function} options.onInvalid - Callback cuando el email es inválido
     */
    window.initEmailValidation = function(inputId, options = {}) {
        const input = document.getElementById(inputId);
        if (!input) {
            console.warn(`Input con ID "${inputId}" no encontrado`);
            return;
        }

        const context = options.context || 'user';
        const excludeId = options.excludeId || null;
        const showMessage = options.showMessage !== false;
        const onValid = options.onValid || null;
        const onInvalid = options.onInvalid || null;

        let messageContainer = null;
        let debounceTimer = null;

        // Crear contenedor de mensaje si no existe
        if (showMessage) {
            messageContainer = document.createElement('div');
            messageContainer.className = 'invalid-feedback';
            messageContainer.style.display = 'none';
            input.parentElement.appendChild(messageContainer);
        }

        /**
         * Verificar email por AJAX
         */
        function checkEmail(email) {
            if (!isValidEmailFormat(email)) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                if (messageContainer) {
                    messageContainer.textContent = 'El formato del email no es válido';
                    messageContainer.className = 'invalid-feedback';
                    messageContainer.style.display = 'block';
                }
                if (onInvalid) onInvalid(email, 'El formato del email no es válido');
                return;
            }

            // Construir URL según el contexto
            let url = '';
            switch(context) {
                case 'administration':
                    url = '/administrations/check-email';
                    break;
                case 'entity':
                    url = '/entities/check-email';
                    break;
                case 'user':
                    url = '/users/check-email';
                    break;
                case 'seller':
                    url = '/sellers/check-email';
                    break;
                default:
                    console.error('Contexto no válido:', context);
                    return;
            }

            // Mostrar estado de carga (opcional, solo visual)
            // input.classList.add('is-loading'); // Comentado para evitar conflictos con Bootstrap

            // Realizar petición AJAX
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                   document.querySelector('input[name="_token"]')?.value || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    exclude_id: excludeId
                })
            })
            .then(response => response.json())
            .then(data => {
                // input.classList.remove('is-loading'); // Comentado para evitar conflictos con Bootstrap

                if (data.exists) {
                    // Email ya está en uso
                    input.classList.remove('is-valid');
                    input.classList.add('is-invalid');
                    if (messageContainer) {
                        messageContainer.textContent = data.message || 'Este email ya está en uso';
                        messageContainer.className = 'invalid-feedback';
                        messageContainer.style.display = 'block';
                    }
                    if (onInvalid) onInvalid(email, data.message || 'Este email ya está en uso');
                } else {
                    // Email disponible
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                    if (messageContainer) {
                        messageContainer.textContent = 'Email disponible';
                        messageContainer.className = 'valid-feedback';
                        messageContainer.style.display = 'block';
                    }
                    if (onValid) onValid(email);
                }
            })
            .catch(error => {
                input.classList.remove('is-valid', 'is-invalid');
                console.error('Error al verificar email:', error);
                // No mostrar error al usuario, solo en consola
            });
        }

        // Validar al perder el foco (blur)
        input.addEventListener('blur', function() {
            const value = this.value.trim();
            
            if (value === '') {
                input.classList.remove('is-valid', 'is-invalid', 'is-loading');
                if (messageContainer) {
                    messageContainer.textContent = '';
                    messageContainer.style.display = 'none';
                }
                return;
            }

            // Limpiar timer anterior
            if (debounceTimer) {
                clearTimeout(debounceTimer);
            }

            // Verificar después de un pequeño delay
            debounceTimer = setTimeout(() => {
                checkEmail(value);
            }, 300);
        });

        // Limpiar validación al escribir
        input.addEventListener('input', function() {
            if (this.value.trim() === '') {
                this.classList.remove('is-valid', 'is-invalid', 'is-loading');
                if (messageContainer) {
                    messageContainer.textContent = '';
                    messageContainer.style.display = 'none';
                }
            } else {
                // Si el formato es válido, mostrar estado neutral mientras se escribe
                if (isValidEmailFormat(this.value.trim())) {
                    this.classList.remove('is-invalid');
                }
            }
        });
    };

})();
