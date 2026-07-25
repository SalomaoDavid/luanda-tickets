{{-- resources/views/livewire/profile-edit.blade.php --}}
<div>

<style>
/* Importa apenas o que não está no CSS partilhado */
.edit-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px;animation:fadeUp .3s ease both;}
.edit-topbar-title{font-size:18px;font-weight:800;color:#fff;}
@media(min-width:768px){.edit-topbar-title{font-size:22px;}}
.edit-topbar-title span{color:var(--accent);}
.edit-section{background:var(--s1);border:1px solid var(--border);border-radius:16px;overflow:hidden;margin-bottom:14px;animation:fadeUp .35s ease both;}
.edit-section-head{padding:14px 18px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:8px;}
.edit-section-icon{width:32px;height:32px;border-radius:9px;background:rgba(6,182,212,.1);border:1px solid var(--border2);display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
.edit-section-title{font-size:13px;font-weight:800;color:var(--text);}
.edit-section-sub{font-size:11px;color:var(--muted);}
.edit-section-body{padding:18px;}
.cover-edit-wrap{height:140px;position:relative;cursor:pointer;background:linear-gradient(135deg,#050d1a,#091828,#0c1f3a);overflow:hidden;border-bottom:1px solid var(--border);}
@media(min-width:768px){.cover-edit-wrap{height:190px;}}
.cover-edit-wrap img{width:100%;height:100%;object-fit:cover;position:absolute;inset:0;}
.cover-edit-btn{position:absolute;top:10px;right:10px;z-index:2;display:flex;align-items:center;gap:5px;background:rgba(6,9,15,.8);backdrop-filter:blur(10px);border:1px solid var(--border);border-radius:9px;padding:6px 10px;font-size:11px;font-weight:600;color:var(--text);cursor:pointer;transition:all .2s;}
.cover-edit-btn:hover{border-color:var(--border2);color:var(--accent);}
.ava-edit-row{display:flex;align-items:center;gap:14px;padding:16px 18px;border-bottom:1px solid var(--border);}
.ava-preview{width:64px;height:64px;border-radius:50%;overflow:hidden;border:3px solid var(--accent);flex-shrink:0;background:linear-gradient(135deg,#0c3a4a,#1e6a7a);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:var(--accent);}
.ava-preview img{width:100%;height:100%;object-fit:cover;}
.upload-hint{font-size:11px;color:var(--muted);margin-top:4px;}
.g2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.delete-zone{background:rgba(244,63,94,.06);border:1px solid rgba(244,63,94,.2);border-radius:12px;padding:16px;}
.delete-zone-title{font-size:13px;font-weight:700;color:#f87171;margin-bottom:6px;}
.delete-zone-sub{font-size:12px;color:var(--muted);margin-bottom:12px;}
.btn-save{width:100%;padding:13px;border-radius:12px;background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;font-size:14px;font-weight:800;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:opacity .15s;}
.btn-save:hover{opacity:.88;}
.btn-danger{width:100%;padding:11px;border-radius:12px;background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.3);color:#f87171;font-size:13px;font-weight:700;cursor:pointer;}
.btn-danger:hover{background:rgba(244,63,94,.18);}
.success-toast{padding:12px 16px;background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);border-radius:10px;color:#34d399;font-size:13px;font-weight:600;margin-bottom:14px;}
</style>

{{-- TOPBAR --}}
<div class="edit-topbar">
    <div class="edit-topbar-title">✏️ Editar <span>Perfil</span></div>
    <div style="display:flex;gap:8px;">
        <a href="{{ route('profile.show', ['id' => auth()->id()]) }}"
           style="padding:8px 14px;border-radius:10px;font-size:12px;font-weight:600;background:var(--s2);border:1px solid var(--border);color:var(--text);text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
            ← Ver Perfil
        </a>
    </div>
</div>

{{-- SUCCESS --}}
@if(session('status') === 'profile-updated')
<div class="success-toast">✅ Perfil actualizado com sucesso!</div>
@endif

{{-- ERROS --}}
@if($errors->any())
<div style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2);border-radius:10px;padding:12px 16px;margin-bottom:14px;color:#f87171;font-size:13px;">
    @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
</div>
@endif

{{-- FORMULÁRIO PRINCIPAL --}}
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
@csrf @method('PATCH')

    {{-- CAPA + AVATAR --}}
    <div class="edit-section">
        {{-- Capa --}}
        <label class="cover-edit-wrap" style="display:block;cursor:pointer;">
            <input type="file" name="cover" accept="image/*" class="sr-only"
                   onchange="previewImg(this,'cover-preview')">
            @if(auth()->user()->cover)
                <img id="cover-preview" src="{{ asset('storage/'.auth()->user()->cover) }}" alt="">
            @else
                <img id="cover-preview" style="display:none;" alt="">
            @endif
            <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 60% 40%,rgba(6,182,212,.18),transparent 55%);pointer-events:none;"></div>
            <div class="cover-edit-btn">📷 Alterar capa</div>
        </label>

        {{-- Avatar --}}
        <div class="ava-edit-row">
            <div class="ava-preview">
                @if(auth()->user()->avatar)
                    <img id="ava-preview" src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                @else
                    <span id="ava-preview-text">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <img id="ava-preview" style="display:none;" alt="">
                @endif
            </div>
            <div>
                <label class="p-label" style="cursor:pointer;color:var(--accent);font-size:12px;">
                    📷 Alterar foto de perfil
                    <input type="file" name="avatar" accept="image/*" class="sr-only"
                           onchange="previewImg(this,'ava-preview')">
                </label>
                <div class="upload-hint">JPG, PNG · máx. 2 MB</div>
            </div>
        </div>

        {{-- Nome + Bio --}}
        <div class="edit-section-body">
            <div class="g2" style="margin-bottom:12px;">
                <div class="p-field" style="margin-bottom:0;">
                    <label class="p-label">Nome</label>
                    <input type="text" name="name" class="p-input"
                           value="{{ old('name', auth()->user()->name) }}" required>
                </div>
                <div class="p-field" style="margin-bottom:0;">
                    <label class="p-label">Email</label>
                    <input type="email" name="email" class="p-input"
                           value="{{ old('email', auth()->user()->email) }}" required>
                </div>
            </div>
            <div class="p-field">
                <label class="p-label">Biografia</label>
                <textarea name="bio" class="p-textarea" rows="3"
                          placeholder="Conta algo sobre ti...">{{ old('bio', auth()->user()->bio) }}</textarea>
            </div>
        </div>
    </div>

    {{-- BOTÃO GUARDAR --}}
    <button type="submit" class="btn-save">
        💾 Guardar Alterações
    </button>
</form>

{{-- APAGAR CONTA --}}
<div style="margin-top:20px;">
    <div class="delete-zone">
        <div class="delete-zone-title">⚠️ Zona de Perigo</div>
        <div class="delete-zone-sub">Apagar a conta é uma acção permanente e irreversível.</div>
        <form method="POST" action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Tens a certeza? Esta acção é irreversível.')">
            @csrf @method('DELETE')
            <div class="p-field" style="margin-bottom:10px;">
                <label class="p-label">Confirma a tua palavra-passe</label>
                <input type="password" name="password" class="p-input" placeholder="••••••••" required>
                @if($errors->userDeletion->first('password'))
                <div class="p-err">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>
            <button type="submit" class="btn-danger">🗑 Apagar Conta Permanentemente</button>
        </form>
    </div>
</div>

<script>
function previewImg(input, previewId) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        const img = document.getElementById(previewId);
        if (img) {
            img.src = e.target.result;
            img.style.display = 'block';
        }
        // Esconde o texto placeholder do avatar se existir
        const txt = document.getElementById('ava-preview-text');
        if (txt) txt.style.display = 'none';
    };
    reader.readAsDataURL(file);
}
</script>

</div>