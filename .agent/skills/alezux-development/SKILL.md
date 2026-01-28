---
name: Alezux Development Standards
description: Reglas y estándares estrictos para el desarrollo del proyecto Alezux Members Beta (Diseño, Arquitectura, Lenguaje).
---

# Alezux Members Beta - Reglas de Desarrollo

Este Skill define las reglas OBLIGATORIAS para todo desarrollo dentro de este proyecto.

## 1. Roles y Mentalidad (Expertise)
Actúa SIEMPRE con el nivel de:
*   **Senior WordPress Developer**: Escribe código robusto, seguro y escalable. Usa siempre las mejores prácticas de WP (hooks, filters, sanitización), evitando hardcoding y código "spaghetti".
*   **Elementor Widget Expert**: Domina la creación de widgets nativos, controles dinámicos y la integración profunda con el editor visual.
*   **LearnDash LMS Expert**: Conocimiento profundo de la estructura de datos, templates y lógica interna de LearnDash.
*   **UI Design Expert**: Obsesión por el detalle visual. Mantén la congruencia absoluta con los estilos del proyecto. Si algo se ve "básico", mejóralo.

## 2. Lenguaje y Comunicación
*   **Español**: TODA la comunicación, explicaciones, comentarios de código y documentación deben estar estrictamente en **ESPAÑOL**.

## 3. Sistema de Diseño (Alezux Design System)
El proyecto utiliza un sistema de diseño "Premium", "High-Tech" y "Minimalist".
ANTES de escribir cualquier CSS, verifica `assets/css/global.css`.

*   **Colores**:
    *   Fondo Base: `#0f0f0f` (`--alezux-bg-base`)
    *   Fondo Tarjetas: `#1a1a1a` (`--alezux-bg-card`)
    *   Acento (Primary): `#6c5ce7` (`--alezux-primary`) - Usar para botones principales y destacados.
    *   Texto Principal: `#ffffff` (`--alezux-text-main`)
    *   Texto Secundario: `#a0a0a0` (`--alezux-text-muted`)
    *   Bordes: `#333333` (`--alezux-border-color`)

*   **Geometría**:
    *   **Border-Radius**: `50px` (`--alezux-border-radius`) para botones y contenedores principales. Bordes rectos NO permitidos a menos que sea estructuralmente necesario.
    *   **Spacing**: Usar variables `--alezux-spacing-md` (20px) y `--alezux-spacing-lg` (40px).

*   **Tipografía**:
    *   Familia: 'Manrope', 'Inter', sans-serif.

*   **Reglas CSS**:
    *   usar **BEM** (Block Element Modifier) para nombres de clases (ej. `.modulo__elemento--modificador`).
    *   NO usar estilos inline (`style="..."`). Todo estilo debe ir en su archivo `.css` correspondiente en `assets/css/` o en la carpeta del módulo.

## 4. Arquitectura del Proyecto
La estructura de directorios es MODULAR. No crear archivos en la raíz si pueden ir en un módulo.

*   **`core/`**: Clases base, loaders, utilidades globales.
*   **`modules/`**: Funcionalidades específicas (ej. `notifications`, `slide-lesson`). Cada módulo debe contener sus propios `assets/` si son específicos.
*   **`assets/`**: Estilos y scripts globales.

## 5. Flujo de Trabajo
1.  **Analizar**: Antes de codificar, revisar si existe un componente reutilizable en `global.css`.
2.  **Modularizar**: Si es una nueva funcionalidad, ¿debería ser un módulo nuevo en `modules/`?
3.  **Estilizar**: Aplicar variables CSS de `global.css`. Asegurar el "Alezux aesthetic" (oscuro, bordes redondeados 50px, acento vibrante).
