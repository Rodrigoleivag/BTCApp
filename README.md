# Bitmine - Plataforma de Inversión en Bitcoin

<p align="center">
<img src="https://laravel.com/assets/img/components/logo-laravel.svg" width="200" alt="Laravel Logo">
</p>

<p align="center">
<a href="#requisitos">Requisitos</a> •
<a href="#instalación">Instalación</a> •
<a href="#configuración">Configuración</a> •
<a href="#uso">Uso</a> •
<a href="#estructura">Estructura</a> •
<a href="#licencia">Licencia</a>
</p>

## 📖 Descripción

Bitmine es una plataforma de inversión en Bitcoin desarrollada con Laravel 5.8. Esta aplicación permite a los usuarios invertir criptomonedas de manera segura y sencilla, con una interfaz amigable y características robustas de gestión.

### Características Principales

- ✅ Gestión de inversiones en Bitcoin y otras criptomonedas
- ✅ Panel de administración completo
- ✅ Múltiples pasarelas de pago (Stripe, CoinGate)
- ✅ Autenticación de dos factores (2FA) con Google Authenticator
- ✅ Sistema de notificaciones SweetAlert
- ✅ Gestión de usuarios y perfiles
- ✅ Soporte multi-moneda
- ✅ Sistema de tickets de soporte
- ✅ Panel de control responsive

## 🔧 Requisitos

Antes de comenzar, asegúrate de tener instalado lo siguiente:

- **PHP**: ^7.1.3
- **Composer**: Gestor de dependencias de PHP
- **MySQL/MariaDB**: Base de datos (versión 10.4+ recomendada)
- **Node.js & NPM**: Para compilación de assets (opcional)
- **Servidor Web**: Apache o Nginx con mod_rewrite habilitado

### Extensiones de PHP Requeridas

- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## 📦 Instalación

Sigue estos pasos para instalar el proyecto en tu entorno local:

### 1. Clonar el Repositorio

```bash
git clone <url-del-repositorio> bitmine
cd bitmine
```

### 2. Instalar Dependencias de PHP

```bash
cd core
composer install
```

### 3. Configurar Variables de Entorno

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configurar la Base de Datos

Crea una base de datos vacía en tu servidor MySQL/MariaDB:

```sql
CREATE DATABASE bitmine CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Importa el archivo SQL proporcionado:

```bash
mysql -u tu_usuario -p bitmine < database.sql
```

O desde phpMyAdmin:
1. Abre phpMyAdmin
2. Selecciona la base de datos `bitmine`
3. Importa el archivo `database.sql`

### 5. Configurar Conexión a la Base de Datos

Edita el archivo `.env` en la carpeta `core` con tus credenciales:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bitmine
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

### 6. Configurar URL de la Aplicación

En el mismo archivo `.env`, actualiza la URL de tu aplicación:

```env
APP_NAME=Bitmine
APP_ENV=production
APP_DEBUG=false
APP_URL=http://tudominio.com
```

### 7. Permisos de Carpetas

Asegúrate de que las carpetas `storage` y `bootstrap/cache` tengan permisos de escritura:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Acceder a la Aplicación

Abre tu navegador y navega a la URL configurada.

**Credenciales de administrador por defecto:**
- **Usuario**: admin
- **Contraseña**: Consulta la base de datos o contacta al desarrollador

## ⚙️ Configuración

### Pasarelas de Pago

La aplicación soporta múltiples pasarelas de pago. Configúralas en el panel de administración o en el archivo `.env`:

#### Stripe
```env
STRIPE_KEY=tu_clave_publica
STRIPE_SECRET=tu_clave_secreta
```

#### CoinGate
```env
COINGATE_API_KEY=tu_api_key
```

### Configuración de Correo Electrónico

Para habilitar el envío de correos, configura SMTP en `.env`:

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Google Authenticator (2FA)

La autenticación de dos factores está integrada. Los usuarios pueden activarla desde su perfil.

## 🚀 Uso

### Panel de Administración

Accede al panel de administración en `/admin` para:

- Gestionar usuarios
- Configurar métodos de pago
- Aprobar retiros
- Ver estadísticas
- Configurar tasas de cambio
- Gestionar contenido del sitio

### Panel de Usuario

Los usuarios registrados pueden:

- Realizar depósitos
- Solicitar retiros
- Ver historial de transacciones
- Activar 2FA
- Actualizar su perfil
- Contactar soporte

## 📁 Estructura del Proyecto

```
bitmine/
├── asset/                  # Assets estáticos (CSS, JS, imágenes)
│   ├── brands/            # Logos de marcas
│   ├── dashboard/         # Assets del panel de control
│   ├── frontend/          # Assets del frontend
│   ├── global_assets/     # Assets globales
│   ├── payment_gateways/  # Iconos de pasarelas de pago
│   └── ...
├── core/                   # Aplicación Laravel principal
│   ├── app/               # Código fuente de la aplicación
│   ├── bootstrap/         # Bootstrap de la aplicación
│   ├── config/            # Archivos de configuración
│   ├── database/          # Migraciones y seeds
│   ├── resources/         # Vistas y recursos no compilados
│   ├── routes/            # Definición de rutas
│   ├── storage/           # Archivos generados
│   └── tests/             # Tests automatizados
├── database.sql           # Dump de la base de datos
├── index.php              # Punto de entrada principal
├── robots.txt             # Configuración para bots
└── web.config             # Configuración para IIS
```

## 🛠️ Comandos Útiles

### Ejecutar en Desarrollo

```bash
cd core
php artisan serve
```

### Limpiar Caché

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimizar para Producción

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔒 Seguridad

### Mejores Prácticas

1. **Nunca** compartas tu archivo `.env`
2. Cambia las credenciales de administrador por defecto
3. Usa HTTPS en producción
4. Mantén actualizadas las dependencias
5. Configura correctamente los permisos de archivos

### Vulnerabilidades de Seguridad

Si descubres una vulnerabilidad de seguridad, por favor envíala a: support@boomchart.com.ng

## 🤝 Soporte

Para soporte técnico, contacta a:
- Email: support@boomchart.com.ng
- Teléfono: +1234567894, +2345666666

## 📄 Licencia

Este proyecto está construido sobre [Laravel](https://laravel.com), un framework de código abierto licenciado bajo los términos de la [licencia MIT](https://opensource.org/licenses/MIT).

## 🙏 Créditos

- Desarrollado con [Laravel 5.8](https://laravel.com/docs/5.8)
- Sistema de alertas: [SweetAlert](https://github.com/realrashid/sweet-alert)
- Procesamiento de imágenes: [Intervention Image](https://image.intervention.io/)
- Autenticación 2FA: [Google Authenticator](https://github.com/sonata-project/GoogleAuthenticator)
- Pasarela de pagos: [Stripe](https://stripe.com/) y [CoinGate](https://coingate.com/)

---

<p align="center">Hecho con ❤️ usando Laravel</p>
