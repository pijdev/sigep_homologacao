{{-- Setup accent theme --}}

@php
$accentTheme = config('ladmin.auth.accent_theme', 'default');
$backgroundClass = config("ladmin.auth.accent_themes.{$accentTheme}.background", 'bg-body-tertiary');
$buttonTheme = config("ladmin.auth.accent_themes.{$accentTheme}.button", 'secondary');
$iconTheme = config("ladmin.auth.accent_themes.{$accentTheme}.icon", 'secondary');
$linkTheme = config("ladmin.auth.accent_themes.{$accentTheme}.link", 'secondary');

// Carregar unidades prisionais
$unidades = \App\Models\UnidadePrisional::orderBy('nome')->get();
@endphp

{{-- Define the layout of the page --}}

<x-ladmin-auth-base title="SIGEP - Login">

    {{-- Login card --}}
    <div class="card shadow">

        {{-- Card header --}}
        <div class="card-header border-bottom-0 {{ $backgroundClass }}">
            <p class="card-title w-100 text-center">
                <strong>SIGEP - Sistema de Gestão Penitenciária</strong><br>
                <small>Entre com suas credenciais</small>
            </p>
        </div>

        {{-- Card body --}}
        <div class="card-body login-card-body rounded-bottom">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Login (usuário) --}}
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="login" name="login" placeholder="Digite seu login" required>
                    <label for="login">Login</label>
                    <span class="input-group-text bg-body-tertiary" style="position: absolute; right: 0; top: 0; height: 100%; border-radius: 0 0.375rem 0.375rem 0;">
                        <i class="bi bi-person-fill fs-5 text-{{ $iconTheme }}"></i>
                    </span>
                </div>

                {{-- Unidade Prisional --}}
                <div class="form-floating mb-3">
                    <select name="unidade_id" class="form-select" required>
                        <option value="">Selecione uma unidade</option>
                        @foreach($unidades as $unidade)
                        <option value="{{ $unidade->id }}" {{ $unidade->id == 8019 ? 'selected' : '' }}>
                            {{ $unidade->nome }}
                        </option>
                        @endforeach
                    </select>
                    <label for="unidade_id">Unidade Prisional</label>
                    <span class="input-group-text bg-body-tertiary" style="position: absolute; right: 0; top: 0; height: 100%; border-radius: 0 0.375rem 0.375rem 0;">
                        <i class="bi bi-building fs-5 text-{{ $iconTheme }}"></i>
                    </span>
                </div>

                {{-- Password --}}
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" required>
                    <label for="password">Senha</label>
                    <span class="input-group-text bg-body-tertiary" style="position: absolute; right: 0; top: 0; height: 100%; border-radius: 0 0.375rem 0.375rem 0;">
                        <i class="bi bi-lock-fill fs-5 text-{{ $iconTheme }}"></i>
                    </span>
                </div>

                {{-- Remember me --}}
                <x-ladmin-checkbox name="remember_me" theme="{{ $buttonTheme }}" label="Lembrar-me"
                    class="shadow-none" sizing="lg" no-validation-feedback />

                {{-- Actions and links --}}
                <div class="d-flex align-items-end mt-3">

                    {{-- Links --}}
                    <div class="flex-grow-1">
                        @if(config('ladmin.auth.features.password_reset', false))
                        <p class="mt-1 mb-0">
                            <a class="link-{{ $linkTheme }}" href="{{ route('password.request') }}">
                                Esqueci minha senha
                            </a>
                        </p>
                        @endif
                    </div>

                    {{-- Sign in button --}}
                    <x-ladmin-button type="submit" theme="{{ $buttonTheme }}" icon="bi bi-box-arrow-in-right fs-5"
                        label="Entrar" class="d-flex align-items-center bg-gradient" />

                </div>
            </form>
        </div>

    </div>

    {{-- Status messages --}}
    @if (session('status'))
    <div class="alert alert-success alert-dismissible fade show text-center shadow mt-3" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i>
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show text-center shadow mt-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i>
        <strong>Erro:</strong> {{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

</x-ladmin-auth-base>
