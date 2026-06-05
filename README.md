# Plataforma de Minería de Criptomonedas

Una aplicación web de minería de criptomonedas construida con Laravel 5.8.

## Descripción

Esta plataforma permite a los usuarios participar en actividades de minería de criptomonedas, gestionar sus cuentas, realizar depósitos y retiros, y monitorear sus ganancias en tiempo real.

## Requisitos del Sistema

- PHP >= 7.1.3
- MySQL / MariaDB
- Composer
- Node.js y NPM (opcional, para assets)
- Servidor web (Apache/Nginx)

## Dependencias Principales

- **Laravel Framework** 5.8.*
- **Intervention Image** - Procesamiento de imágenes
- **Stripe** - Pasarela de pagos
- **CoinGate** - Pagos con criptomonedas
- **Sweet Alert** - Notificaciones elegantes
- **Google Authenticator** - Autenticación de dos factores

## Instalación

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd <directorio-del-proyecto>
```

### 2. Instalar dependencias de PHP

```bash
cd core
composer install
```

### 3. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Editar el archivo `.env` y configurar:
- `DB_DATABASE` - Nombre de la base de datos
- `DB_USERNAME` - Usuario de la base de datos
- `DB_PASSWORD` - Contraseña de la base de datos
- Credenciales de Stripe, CoinGate y otros servicios

### 4. Importar la base de datos

```bash
mysql -u <usuario> -p <nombre_base_datos> < database.sql
```

### 5. Configurar permisos

```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 6. Acceder a la aplicación

Apunta tu servidor web al directorio raíz del proyecto o usa el servidor de desarrollo:

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## Estructura del Proyecto

```
/
├── asset/              # Recursos estáticos (imágenes, CSS, JS)
│   ├── dashboard/      # Assets del panel de administración
│   ├── frontend/       # Assets del frontend público
│   └── global_assets/  # Assets globales
├── core/               # Aplicación Laravel principal
│   ├── app/            # Lógica de negocio
│   ├── config/         # Archivos de configuración
│   ├── database/       # Migraciones y seeds
│   ├── resources/      # Vistas y recursos compilables
│   └── routes/         # Definición de rutas
├── database.sql        # Dump de la base de datos
├── index.php           # Punto de entrada principal
└── robots.txt          # Configuración para bots
```

## Características

- Panel de administración
- Gestión de usuarios
- Sistema de minería
- Múltiples pasarelas de pago (Stripe, CoinGate)
- Autenticación de dos factores (2FA)
- Soporte multi-moneda
- Sistema de notificaciones
- Gestión de perfiles
- Historial de transacciones

## Seguridad

- Hash de contraseñas con bcrypt
- Protección CSRF
- Validación de formularios
- Autenticación de dos factores
- Sanitización de entradas

## Licencia

Este proyecto es software propietario. Todos los derechos reservados.

## Soporte

Para soporte técnico, por favor contacte al administrador del sistema.

---

**Nota:** Esta aplicación está diseñada para ser ejecutada en un entorno seguro y bajo las regulaciones aplicables de criptomonedas en su jurisdicción.
