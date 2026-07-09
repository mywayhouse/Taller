# Taller Mecánico - Sistema de Gestión

## Instalación para nuevos desarrolladores

1. Clonar el repositorio:
   ```bash
   git clone <repo-url>
   cd Taller
   ```

2. Instalar dependencias:
   ```bash
   composer install --no-interaction
   ```

3. Copiar y configurar entorno:
   ```bash
   cp .env.example .env
   # Editar .env si es necesario (por defecto root sin contraseña en localhost)
   ```

4. Ejecutar instalador (crea BD, importa SQL, resetea contraseñas):
   ```bash
   php install.php
   ```

5. Acceder desde el navegador:
   ```
   http://localhost/Taller
   ```

### Usuarios por defecto
| Correo | Contraseña | Rol |
|---|---|---|
| admin@taller.com | admin123 | ADMINISTRADOR |
| ana@taller.com | admin123 | RECEPCIONISTA |
| juan@taller.com | admin123 | MECANICO |

## Flujo de trabajo (Branching Strategy)

- `main`: código estable, protegido, listo para entrega.
- `feature/*`: una rama por módulo del sistema.

Ramas activas:
- feature/backend-login (Kevin)
- feature/roles-auditoria (Erwin)
- feature/idiomas-dialogos (Dixy)
- feature/clientes (Erick)
- feature/vehiculos (Ricardo)
- feature/repuestos-inventario (Evelyn)

Reglas:
1. Nunca trabajar directo en `main`.
2. Cada programador trabaja en su rama `feature/*` correspondiente.
3. Commits pequeños y descriptivos.
4. Al terminar, abrir un Pull Request hacia `main`.
5. El Git Master revisa y aprueba antes de fusionar.
