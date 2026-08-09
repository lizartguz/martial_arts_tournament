# 🎨 Guía de Personalización de Colores Temáticos

## Introducción

Este sistema utiliza **variables CSS personalizables** que permiten cambiar el color de toda la aplicación desde un único archivo. Los colores se aplican automáticamente en todos los componentes, tablas, botones y elementos de la interfaz.

## 📍 Ubicación del Archivo de Colores

El archivo principal de configuración de colores está en:

```
public/css/theme/themes.css
```

## 🎯 Variables CSS Disponibles

### Colores Principales

```css
:root {
    /* Color principal - Verde Esmeralda para aplicaciones ambientales */
    --theme-primary: #047857;           /* Verde esmeralda oscuro */
    --theme-primary-light: #10b981;     /* Verde esmeralda claro */
    --theme-primary-dark: #065f46;      /* Verde esmeralda más oscuro */
    --theme-primary-lighter: #059669;   /* Verde medio */
}
```

### Colores de Gradientes

```css
    /* Colores de gradientes */
    --theme-gradient-start: var(--theme-primary);
    --theme-gradient-end: var(--theme-primary-light);
    --theme-gradient-hover-start: var(--theme-primary-dark);
    --theme-gradient-hover-end: var(--theme-primary-lighter);
```

### Fondos

```css
    /* Fondos con transparencia */
    --theme-bg-light: #f0fdf4;          /* Fondo verde muy claro */
    --theme-bg-hover: #ecfdf5;          /* Fondo verde hover */
```

### Sombras Temáticas

```css
    /* Sombras temáticas */
    --theme-shadow-sm: 0 2px 4px rgba(4, 120, 87, 0.2);
    --theme-shadow-md: 0 4px 6px rgba(4, 120, 87, 0.3);
    --theme-shadow-lg: 0 6px 12px rgba(4, 120, 87, 0.4);
    --theme-shadow-xl: 0 8px 16px rgba(4, 120, 87, 0.5);
```

## 🔧 Cómo Cambiar el Color de Toda la Aplicación

### Opción 1: Verde (Actual - Ambiental) 🌿

```css
--theme-primary: #047857;           /* Verde esmeralda oscuro */
--theme-primary-light: #10b981;     /* Verde esmeralda claro */
--theme-primary-dark: #065f46;
--theme-primary-lighter: #059669;
--theme-bg-light: #f0fdf4;
```

### Opción 2: Azul (Corporativo) 💙

```css
--theme-primary: #1e40af;           /* Azul oscuro */
--theme-primary-light: #3b82f6;     /* Azul claro */
--theme-primary-dark: #1e3a8a;
--theme-primary-lighter: #2563eb;
--theme-bg-light: #eff6ff;
```

### Opción 3: Púrpura (Tecnológico) 💜

```css
--theme-primary: #7c3aed;           /* Púrpura oscuro */
--theme-primary-light: #a78bfa;     /* Púrpura claro */
--theme-primary-dark: #6d28d9;
--theme-primary-lighter: #8b5cf6;
--theme-bg-light: #f5f3ff;
```

### Opción 4: Naranja (Energético) 🧡

```css
--theme-primary: #ea580c;           /* Naranja oscuro */
--theme-primary-light: #fb923c;     /* Naranja claro */
--theme-primary-dark: #c2410c;
--theme-primary-lighter: #f97316;
--theme-bg-light: #fff7ed;
```

### Opción 5: Turquesa (Marino) 🌊

```css
--theme-primary: #0891b2;           /* Turquesa oscuro */
--theme-primary-light: #06b6d4;     /* Turquesa claro */
--theme-primary-dark: #0e7490;
--theme-primary-lighter: #22d3ee;
--theme-bg-light: #ecfeff;
```

### Opción 6: Rojo (Alerta) ❤️

```css
--theme-primary: #dc2626;           /* Rojo oscuro */
--theme-primary-light: #ef4444;     /* Rojo claro */
--theme-primary-dark: #b91c1c;
--theme-primary-lighter: #f87171;
--theme-bg-light: #fef2f2;
```

## 📝 Instrucciones Paso a Paso

1. **Abre el archivo de configuración:**
   ```
   public/css/theme/themes.css
   ```

2. **Busca la sección de variables (líneas 17-40):**
   ```css
   /* Color principal - Verde Esmeralda para aplicaciones ambientales */
   --theme-primary: #047857;
   ```

3. **Reemplaza los valores con tu color preferido:**
   - Copia una de las opciones de arriba
   - Pega los nuevos valores en el archivo
   - Guarda el archivo

4. **Actualiza el navegador:**
   - Presiona `Ctrl + F5` (o `Cmd + Shift + R` en Mac)
   - Los cambios se aplicarán inmediatamente en toda la aplicación

## 🎯 Componentes Afectados

Los siguientes componentes utilizan automáticamente estos colores:

### ✅ Tablas
- Headers de tablas
- Filas hover
- Scrollbars personalizados

### ✅ Botones
- Botones primarios
- Botones de descarga
- Botones de búsqueda

### ✅ Cards
- Headers de cards
- Bordes activos

### ✅ Formularios
- Inputs en focus
- Select boxes

### ✅ Alertas
- Alertas de información
- Badges

### ✅ Dropdowns
- Menús desplegables
- Items hover

## 🔍 Personalización Avanzada

### Ajustar Sombras

Si cambias el color principal, también deberías ajustar las sombras para que coincidan:

```css
/* Ejemplo para azul */
--theme-shadow-sm: 0 2px 4px rgba(30, 64, 175, 0.2);   /* Ajusta RGB al color */
--theme-shadow-md: 0 4px 6px rgba(30, 64, 175, 0.3);
--theme-shadow-lg: 0 6px 12px rgba(30, 64, 175, 0.4);
--theme-shadow-xl: 0 8px 16px rgba(30, 64, 175, 0.5);
```

### Convertir HEX a RGB

Para ajustar las sombras, necesitas convertir tu color HEX a RGB:

**Ejemplo:**
- `#047857` = `rgb(4, 120, 87)`
- `#1e40af` = `rgb(30, 64, 175)`

## 📊 Ejemplos de Uso en el Código

Los componentes usan las variables así:

```css
/* Botón con gradiente */
.btn-primary {
    background: linear-gradient(135deg, 
        var(--theme-gradient-start) 0%, 
        var(--theme-gradient-end) 100%
    );
}

/* Header de tabla */
.table-header {
    background: linear-gradient(135deg, 
        var(--theme-gradient-start) 0%, 
        var(--theme-gradient-end) 100%
    );
}

/* Input en focus */
.form-input:focus {
    border-color: var(--theme-primary-light);
    background-color: var(--theme-bg-light);
}

/* Sombra de botón */
.btn:hover {
    box-shadow: var(--theme-shadow-md);
}
```

## 🚀 Ventajas de Este Sistema

1. ✅ **Centralizado**: Cambia un solo archivo
2. ✅ **Consistente**: Todos los componentes usan los mismos colores
3. ✅ **Fácil**: No necesitas buscar en múltiples archivos
4. ✅ **Rápido**: Los cambios se aplican instantáneamente
5. ✅ **Mantenible**: Fácil de actualizar en el futuro
6. ✅ **Escalable**: Agrega nuevos componentes y usan los mismos colores automáticamente

## 🎨 Recomendaciones de Colores por Tipo de Aplicación

- **🌿 Ambiental/Climatología**: Verde (Actual)
- **💧 Recursos Hídricos**: Azul/Turquesa
- **🌞 Energía Solar**: Naranja/Amarillo
- **🏭 Industrial**: Gris/Azul oscuro
- **🔬 Científica**: Púrpura/Azul
- **🌡️ Meteorología**: Azul/Celeste

## 📞 Soporte

Si tienes problemas o dudas sobre la personalización de colores, consulta este documento o contacta al equipo de desarrollo.

---

**Última actualización:** Noviembre 2025
**Versión:** 1.0
**Sistema:** SenvaTec Environmental

