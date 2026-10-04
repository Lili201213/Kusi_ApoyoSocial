<label class="form-label">Beneficiario *</label>
<input type="text" id="filtroBenef" class="form-control mb-1" placeholder="Escribe DNI, nombre o apellido para filtrar la lista...">
<select name="beneficiario_id" id="selBenef" class="form-select @error('beneficiario_id') is-invalid @enderror" required>
    <option value="">— Seleccionar beneficiario —</option>
    @foreach ($beneficiarios as $b)
        <option value="{{ $b->id }}"
                data-texto="{{ mb_strtolower($b->dni.' '.$b->apellidos.' '.$b->nombres) }}"
                @selected(old('beneficiario_id', $seleccionado) == $b->id)>
            {{ $b->dni }} — {{ $b->apellidos }}, {{ $b->nombres }}
        </option>
    @endforeach
</select>
@error('beneficiario_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

@push('scripts')
<script>
    (function () {
        const filtro = document.getElementById('filtroBenef');
        const sel = document.getElementById('selBenef');
        filtro.addEventListener('input', function () {
            const t = filtro.value.trim().toLowerCase();
            const visibles = [];
            Array.from(sel.options).forEach(function (o, i) {
                if (i === 0) return;
                const ok = !t || o.dataset.texto.includes(t);
                o.hidden = !ok;
                if (ok) visibles.push(o);
            });
            if (t && visibles.length === 1) sel.value = visibles[0].value;
        });
    })();
</script>
@endpush
