<span class="selector-idioma">
    @foreach (['es' => 'ES', 'en' => 'EN', 'fr' => 'FR'] as $codigo => $etiqueta)
        <a href="{{ route('idioma', $codigo) }}"
           style="color:inherit; text-decoration:none; font-weight: {{ app()->getLocale() === $codigo ? '700' : '400' }}; {{ app()->getLocale() === $codigo ? 'text-decoration:underline;' : '' }}">
            {{ $etiqueta }}
        </a>@if (!$loop->last) | @endif
    @endforeach
</span>
