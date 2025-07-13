/**
 * This configuration was generated using the CKEditor 5 Builder. You can modify it anytime using this link:
 * https://ckeditor.com/ckeditor-5/builder/#installation/NoDgNARATAdAbDAjBSiAMVEFZGIOyIAsecaeZIIcRAzITXsQJxo1pMhbWJNQgoQApgDsUNMMERgpaaWFmIAupDwBjYqpoAjCIqA=
 */

const {
	DecoupledEditor,
	Alignment,
	AutoImage,
	Autosave,
	BalloonToolbar,
	BlockQuote,
	Bold,
	CloudServices,
	Code,
	Essentials,
	FontColor,
	FontFamily,
	FontSize,
	Heading,
	HorizontalLine,
	ImageBlock,
	ImageEditing,
	ImageInsertViaUrl,
	ImageToolbar,
	ImageUpload,
	ImageUtils,
	Indent,
	IndentBlock,
	Italic,
	List,
	Paragraph,
	Underline
} = window.CKEDITOR;

/**
 * This is a 24-hour evaluation key. Create a free account to use CDN: https://portal.ckeditor.com/checkout?plan=free
 */
const LICENSE_KEY =
	'eyJhbGciOiJFUzI1NiJ9.eyJleHAiOjE3NTE2NzM1OTksImp0aSI6IjhlN2Q0MGFmLWI2MTgtNGE3OS05MzViLTNiMjA5YmU1YzFhNSIsInVzYWdlRW5kcG9pbnQiOiJodHRwczovL3Byb3h5LWV2ZW50LmNrZWRpdG9yLmNvbSIsImRpc3RyaWJ1dGlvbkNoYW5uZWwiOlsiY2xvdWQiLCJkcnVwYWwiLCJzaCJdLCJ3aGl0ZUxhYmVsIjp0cnVlLCJsaWNlbnNlVHlwZSI6InRyaWFsIiwiZmVhdHVyZXMiOlsiKiJdLCJ2YyI6IjBkNzAxOGZiIn0.9Tw0ZuzGTkVytzZCjaYEhkIhrFY5Il8T8s70w4cFYIBkoHQbHO7jjjDcS6pPWUlQloWdDjldCmB1SWeTUTnWfg';

const editorConfig = {
	toolbar: {
		items: [
			'undo',
			'redo',
			'|',
			'heading',
			'|',
			'fontSize',
			'fontFamily',
			'fontColor',
			'|',
			'bold',
			'italic',
			'underline',
			'code',
			'|',
			'horizontalLine',
			'blockQuote',
			'|',
			'alignment',
			'|',
			'bulletedList',
			'numberedList',
			'outdent',
			'indent'
		],
		shouldNotGroupWhenFull: false
	},
	plugins: [
		Alignment,
		AutoImage,
		Autosave,
		BalloonToolbar,
		BlockQuote,
		Bold,
		CloudServices,
		Code,
		Essentials,
		FontColor,
		FontFamily,
		FontSize,
		Heading,
		HorizontalLine,
		ImageBlock,
		ImageEditing,
		ImageInsertViaUrl,
		ImageToolbar,
		ImageUpload,
		ImageUtils,
		Indent,
		IndentBlock,
		Italic,
		List,
		Paragraph,
		Underline
	],
	balloonToolbar: ['bold', 'italic', '|', 'bulletedList', 'numberedList'],
	fontFamily: {
		supportAllValues: true
	},
	fontSize: {
		options: [6,8,10,12,14,'default',18,20,22,26,30,34,38,42],
		supportAllValues: true
	},
	heading: {
		options: [
			{
				model: 'paragraph',
				title: 'Paragraph',
				class: 'ck-heading_paragraph'
			},
			{
				model: 'heading1',
				view: 'h1',
				title: 'Heading 1',
				class: 'ck-heading_heading1'
			},
			{
				model: 'heading2',
				view: 'h2',
				title: 'Heading 2',
				class: 'ck-heading_heading2'
			},
			{
				model: 'heading3',
				view: 'h3',
				title: 'Heading 3',
				class: 'ck-heading_heading3'
			},
			{
				model: 'heading4',
				view: 'h4',
				title: 'Heading 4',
				class: 'ck-heading_heading4'
			},
			{
				model: 'heading5',
				view: 'h5',
				title: 'Heading 5',
				class: 'ck-heading_heading5'
			},
			{
				model: 'heading6',
				view: 'h6',
				title: 'Heading 6',
				class: 'ck-heading_heading6'
			}
		]
	},
	image: {
		toolbar: []
	},
	initialData:
		"<p>Escribe aquí...</p>",
	licenseKey: LICENSE_KEY,
	placeholder: 'Escribe aquí...'
};

// Script para limpiar la configuración del tema y respetar los valores del HTML
document.addEventListener('DOMContentLoaded', function() {
    // Limpiar sessionStorage para usar valores del HTML
    if (sessionStorage.getItem("__UBOLD_CONFIG__")) {
        sessionStorage.removeItem("__UBOLD_CONFIG__");
        console.log("Configuración del tema limpiada. Usando valores del HTML.");
    }
    
    // Forzar la aplicación de los valores del HTML
    const html = document.documentElement;
    
    // Verificar y aplicar valores si no están establecidos
    if (!html.getAttribute("data-bs-theme")) {
        html.setAttribute("data-bs-theme", "light");
    }
    if (!html.getAttribute("data-layout-mode")) {
        html.setAttribute("data-layout-mode", "detached");
    }
    if (!html.getAttribute("data-topbar-color")) {
        html.setAttribute("data-topbar-color", "light");
    }
    if (!html.getAttribute("data-menu-color")) {
        html.setAttribute("data-menu-color", "light");
    }
    if (!html.getAttribute("data-sidenav-size")) {
        html.setAttribute("data-sidenav-size", "default");
    }
    
    console.log("Valores del tema aplicados desde el HTML:");
    console.log("data-bs-theme:", html.getAttribute("data-bs-theme"));
    console.log("data-layout-mode:", html.getAttribute("data-layout-mode"));
    console.log("data-topbar-color:", html.getAttribute("data-topbar-color"));
    console.log("data-menu-color:", html.getAttribute("data-menu-color"));
    console.log("data-sidenav-size:", html.getAttribute("data-sidenav-size"));
});

// Función para resetear la configuración del tema
function resetThemeSettings() {
    sessionStorage.removeItem("__UBOLD_CONFIG__");
    location.reload();
}

// Agregar función global para resetear tema
window.resetThemeSettings = resetThemeSettings;