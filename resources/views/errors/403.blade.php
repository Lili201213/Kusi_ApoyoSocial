<x-error-layout
    codigo="403"
    icono="bi-shield-lock"
    :titulo="__('Acceso no permitido')"
    :mensaje="$exception->getMessage() ?: __('No tienes permiso para acceder a esta sección. Si lo necesitas, pídelo al administrador.')" />
