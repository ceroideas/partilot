/**
 * Validador de documentos españoles (DNI, NIE, CIF)
 * Implementa el algoritmo de validación del módulo 23 y CIF
 */

(function() {
    'use strict';

    /**
     * Validar NIF (8 dígitos + 1 letra)
     */
    function validateNif(document) {
        const nifRegex = /^[0-9]{8}[TRWAGMYFPDXBNJZSQVHLCKE]$/i;
        if (!nifRegex.test(document)) {
            return false;
        }

        const number = document.substring(0, 8);
        const letter = document.substring(8, 9).toUpperCase();
        const letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        const expectedLetter = letters[parseInt(number) % 23];

        return letter === expectedLetter;
    }

    /**
     * Validar NIE (X/Y/Z + 7 dígitos + 1 letra)
     */
    function validateNie(document) {
        const nieRegex = /^[XYZ][0-9]{7}[TRWAGMYFPDXBNJZSQVHLCKE]$/i;
        if (!nieRegex.test(document)) {
            return false;
        }

        const firstChar = document.substring(0, 1).toUpperCase();
        const number = document.substring(1, 8);
        const letter = document.substring(8, 9).toUpperCase();

        // Reemplazar X, Y, Z por 0, 1, 2
        const numberForCalc = (firstChar === 'X' ? '0' : firstChar === 'Y' ? '1' : '2') + number;
        const letters = 'TRWAGMYFPDXBNJZSQVHLCKE';
        const expectedLetter = letters[parseInt(numberForCalc) % 23];

        return letter === expectedLetter;
    }

    /**
     * Calcula el dígito de control CIF duplicando los dígitos en las posiciones indicadas (0-6).
     */
    function cifControlDigit(number, doublePositions) {
        let sum = 0;
        for (let i = 0; i < 7; i++) {
            const digit = parseInt(number[i], 10);
            if (doublePositions.indexOf(i) !== -1) {
                const doubled = digit * 2;
                sum += Math.floor(doubled / 10) + (doubled % 10);
            } else {
                sum += digit;
            }
        }
        return (10 - (sum % 10)) % 10;
    }

    /**
     * Validar CIF (1 letra + 7 dígitos + 1 letra/dígito).
     * Para tipo G (asociaciones, clubes) se aceptan número o letra de control
     * y la variante con doble solo en posiciones 0,2,4 (ej. G48123987).
     */
    function validateCif(document) {
        const cifRegex = /^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/i;
        if (!cifRegex.test(document)) {
            return false;
        }

        const firstChar = document.substring(0, 1).toUpperCase();
        const number = document.substring(1, 8);
        const control = document.substring(8, 9).toUpperCase();
        const letters = 'JABCDEFGHI';

        const checkStandard = cifControlDigit(number, [0, 2, 4, 6]);
        const validStandard = control === checkStandard.toString() || control === letters[checkStandard];

        // A, B, E, H: solo dígito numérico
        if (['A', 'B', 'E', 'H'].includes(firstChar)) {
            return control === checkStandard.toString();
        }

        // G (asociaciones, clubes, etc.): número o letra; y variante con doble solo 0,2,4
        if (firstChar === 'G') {
            const checkAlternate = cifControlDigit(number, [0, 2, 4]);
            const validAlternate = control === checkAlternate.toString() || control === letters[checkAlternate];
            return validStandard || validAlternate;
        }

        // Resto: letra de control
        return control === letters[checkStandard];
    }

    /**
     * Validar documento español. Orden: 1º NIF, 2º NIE (y TIE, mismo formato), 3º CIF.
     * Opción forEntity: true para mensajes de entidades (NIF, NIE, TIE o CIF).
     */
    window.validateSpanishDocument = function(document, options = {}) {
        if (!document || document.trim() === '') {
            return { valid: true, message: '' };
        }

        const doc = document.toUpperCase().trim().replace(/\s/g, '');
        const forEntity = options && options.forEntity === true;

        if (validateNif(doc)) {
            return { valid: true, message: 'NIF válido' };
        }
        if (validateNie(doc)) {
            return { valid: true, message: forEntity ? 'NIE/TIE válido' : 'NIE válido' };
        }
        if (validateCif(doc)) {
            return { valid: true, message: 'CIF válido' };
        }

        return { 
            valid: false, 
            message: forEntity 
                ? 'El documento debe ser un NIF, NIE, TIE o CIF válido' 
                : 'El documento no es un NIF, NIE o CIF válido' 
        };
    };

    /**
     * Inicializar validación en tiempo real para un input
     */
    window.initSpanishDocumentValidation = function(inputId, options = {}) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const {
            showMessage = true,
            messageContainerId = null,
            onValid = null,
            onInvalid = null,
            forEntity = false
        } = options;

        let messageContainer = null;
        if (showMessage) {
            if (messageContainerId) {
                messageContainer = document.getElementById(messageContainerId);
            } else {
                // Crear contenedor de mensaje si no existe
                messageContainer = document.createElement('div');
                messageContainer.className = 'invalid-feedback';
                messageContainer.style.display = 'block';
                input.parentElement.appendChild(messageContainer);
            }
        }

        input.addEventListener('blur', function() {
            const value = this.value.trim();
            
            if (value === '') {
                // Si está vacío y es opcional, es válido
                if (input.hasAttribute('required')) {
                    input.classList.remove('is-valid', 'is-invalid');
                    if (messageContainer) messageContainer.textContent = '';
                } else {
                    input.classList.remove('is-valid', 'is-invalid');
                    if (messageContainer) messageContainer.textContent = '';
                }
                return;
            }

            const result = validateSpanishDocument(value, { forEntity: forEntity });

            if (result.valid) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                if (messageContainer) {
                    messageContainer.textContent = result.message;
                    messageContainer.className = 'valid-feedback';
                    messageContainer.style.display = 'block';
                }
                if (onValid) onValid(value);
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                if (messageContainer) {
                    messageContainer.textContent = result.message;
                    messageContainer.className = 'invalid-feedback';
                    messageContainer.style.display = 'block';
                }
                if (onInvalid) onInvalid(value, result.message);
            }
        });

        // Limpiar validación al escribir
        input.addEventListener('input', function() {
            if (this.value.trim() === '') {
                this.classList.remove('is-valid', 'is-invalid');
                if (messageContainer) messageContainer.textContent = '';
            }
        });
    };

})();
