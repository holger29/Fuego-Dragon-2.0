// Función para inicializar las partículas con la configuración deseada
tsParticles.load({
    id: "tsparticles",
    options: {
        // La configuración de fondo ya está en el CSS, pero la definimos aquí también
        background: {
            color: {
                value: "#121212",
            },
        },
        fpsLimit: 60,
        interactivity: {
            events: {
                // Habilitamos que las partículas reaccionen al pasar el mouse
                onHover: {
                    enable: true,
                    mode: "bubble", // o "repulse" para un efecto más dramático
                },
                resize: true,
            },
            modes: {
                // Configuración de la interacción "burbuja"
                bubble: {
                    distance: 200,
                    duration: 2,
                    opacity: 0.6,
                    size: 8, // Aumenta el tamaño al acercar el mouse
                },
            },
        },
        particles: {
            color: {
                // Múltiples colores para simular el fuego/chispas
                value: ["#ff7f00", "#ff0000", "#ffffff"], // Naranja, Rojo, Blanco (Chispa)
            },
            links: {
                enable: false, // Desactivar las líneas entre partículas (para simular chispas separadas)
            },
            move: {
                direction: "top", // Las chispas se mueven ligeramente hacia arriba
                enable: true,
                outModes: {
                    default: "out",
                },
                random: true,
                speed: 0.5, // Velocidad baja para un efecto atmosférico
                straight: false,
            },
            number: {
                density: {
                    enable: true,
                    area: 800,
                },
                value: 80, // Número total de partículas
            },
            opacity: {
                value: { min: 0.1, max: 0.5 }, // Opacidad variable para un efecto más natural
                animation: {
                    enable: true,
                    speed: 0.5,
                    sync: false,
                    startValue: "max",
                    destroy: "min"
                }
            },
            shape: {
                type: "circle",
            },
            size: {
                value: { min: 1, max: 3 }, // Tamaño pequeño
                random: true,
            },
        },
        detectRetina: true,
    },
});