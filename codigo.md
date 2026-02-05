selector {
    overflow-y: auto !important;
    scrollbar-width: none !important;       /* Firefox */
    -ms-overflow-style: none !important;    /* IE y Edge antiguo */
}

/* Ocultar en Chrome, Safari y Edge moderno */
selector::-webkit-scrollbar {
    display: none !important;
    width: 0px !important;
    background: transparent !important;
}

/* Solo aplica en escritorio (pantallas mayores a 1024px) */
@media (min-width: 1025px) {
    /* Empujamos todo el cuerpo de la web a la derecha */
    body {
        margin-left: 250px !important; /* Mismo ancho que tu sidebar */
        width: calc(100% - 250px) !important; /* Evita scroll horizontal innecesario */
    }
    
    /* Opcional: Si usas secciones de "Ancho Completo" que se rompen, 
       forzamos el ancho máximo de los contenedores de Elementor */
    .elementor-section-stretched {
        left: 250px !important;
        width: calc(100% - 250px) !important;
    }
}
