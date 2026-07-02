# Taller
## Flujo de trabajo (Branching Strategy)

- `main`: código estable, protegido, listo para entrega.
- `feature/*`: una rama por módulo del sistema.

Ramas activas:
- feature/clientes-vehiculos
- feature/inventario-repuestos
- feature/ordenes-servicio
- feature/facturacion
- feature/estadisticas-dashboard

Reglas:
1. Nunca trabajar directo en `main`.
2. Cada programador trabaja en su rama `feature/*` correspondiente.
3. Commits pequeños y descriptivos.
4. Al terminar, abrir un Pull Request hacia `main`.
5. El Git Master revisa y aprueba antes de fusionar.
