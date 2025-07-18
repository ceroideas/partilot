/**
* Theme: Ubold - Responsive Bootstrap 5 Admin Dashboard
* Author: Coderthemes
* Module/App: Theme Config Js
*/

(function () {
    var savedConfig = sessionStorage.getItem("__UBOLD_CONFIG__");
    // var savedConfig = localStorage.getItem("__UBOLD_CONFIG__");

    var html = document.getElementsByTagName("html")[0];

    //  Default Config Value
    var defaultConfig = {
        theme: "light",

        layout: {
            mode: "default",
            width: "default",
        },

        topbar: {
            color: "light",
        },

        menu: {
            color: "light",
            icon: "default",
        },

        // This option for only vertical (left Sidebar) layout
        sidenav: {
            size: "default",
            twocolumn: "light",
            user: false,
        },
    };

    this.html = document.getElementsByTagName('html')[0];

    config = Object.assign(JSON.parse(JSON.stringify(defaultConfig)), {});

    var layoutColor = this.html.getAttribute('data-bs-theme');
    config['theme'] = layoutColor !== null ? layoutColor : defaultConfig.theme;

    var layoutMode = this.html.getAttribute('data-layout-mode');
    config['layout']['mode'] = layoutMode != null ? layoutMode : defaultConfig.layout.mode;

    var layoutWidth = this.html.getAttribute('data-layout-width');
    config['layout']['width'] = layoutWidth != null ? layoutWidth : defaultConfig.layout.width;

    var topbarColor = this.html.getAttribute('data-topbar-color');
    config['topbar']['color'] = topbarColor != null ? topbarColor : defaultConfig.topbar.color;

    var leftbarSize = this.html.getAttribute('data-sidenav-size');
    config['sidenav']['size'] = leftbarSize !== null ? leftbarSize : defaultConfig.sidenav.size;

    var sidebarUser = this.html.getAttribute('data-sidenav-user')
    config['sidenav']['user'] = sidebarUser !== null ? true : defaultConfig.sidenav.user;

    var menuColor = this.html.getAttribute('data-menu-color');
    config['menu']['color'] = menuColor !== null ? menuColor : defaultConfig.menu.color;

    var menuIcon = this.html.getAttribute('data-menu-icon');
    config['menu']['icon'] = menuIcon !== null ? menuIcon : defaultConfig.menu.icon;

    var twocolumnColor = this.html.getAttribute('data-two-column-color');
    config['sidenav']['twocolumn'] = twocolumnColor !== null ? twocolumnColor : defaultConfig.sidenav.twocolumn;

    window.defaultConfig = JSON.parse(JSON.stringify(config));

    // Limpiar configuración guardada para usar valores del HTML
    if (savedConfig !== null) {
        // Comentar esta línea para usar valores del HTML en lugar de sessionStorage
        // config = JSON.parse(savedConfig);
        sessionStorage.removeItem("__UBOLD_CONFIG__");
    }

    window.config = config;

    if (config) {
        // Solo aplicar configuración si no hay valores ya establecidos en el HTML
        if (!html.getAttribute("data-bs-theme")) {
            html.setAttribute("data-bs-theme", config.theme);
        }
        if (!html.getAttribute("data-layout-mode")) {
            html.setAttribute("data-layout-mode", config.layout.mode);
        }
        if (!html.getAttribute("data-layout-width")) {
            html.setAttribute("data-layout-width", config.layout.width);
        }
        if (!html.getAttribute("data-topbar-color")) {
            html.setAttribute("data-topbar-color", config.topbar.color);
        }
        if (!html.getAttribute("data-menu-color")) {
            html.setAttribute("data-menu-color", config.menu.color);
        }
        if (!html.getAttribute("data-menu-icon")) {
            html.setAttribute("data-menu-icon", config.menu.icon);
        }
        if (!html.getAttribute("data-sidenav-size")) {
            html.setAttribute("data-sidenav-size", config.sidenav.size);
        }

        if (html.getAttribute("data-layout") === "two-column" && !html.getAttribute("data-two-column-color")) {
            html.setAttribute("data-two-column-color", config.sidenav.twocolumn);
        }

        if (config.sidenav.user && config.sidenav.user.toString() === "true" && !html.getAttribute("data-sidenav-user")) {
            html.setAttribute("data-sidenav-user", true);
        } else if (!config.sidenav.user || config.sidenav.user.toString() !== "true") {
            html.removeAttribute("data-sidenav-user");
        }
    }

    if (window.innerWidth <= 1040) {
        html.setAttribute("data-sidenav-size", "full");
        if (html.getAttribute("data-layout") === "horizontal") {
            html.setAttribute("data-layout", "vertical");
        }
    }

})();