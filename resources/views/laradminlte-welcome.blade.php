<x-ladmin-panel title="Início">

    {{-- Setup you content header --}}
    <x-slot name="contentHeader">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold">
                    <i class="bi bi-heart-fill text-danger"></i>
                    Bem-vindo ao SIGEP - Ambiente de Homologação
                </h3>
            </div>
        </div>
    </x-slot>

    {{-- Setup you content body --}}
    <div class="row">
        <div class="col-12">
            <i class="bi bi-rocket-takeoff-fill fs-5 text-primary"></i>
            Sistema Prisional Integrado
        </div>
    </div>

    {{-- Push inline scripts if needed --}}
    @push('js')
    <script>
        console.log('SIGEP Homologação iniciado!');
    </script>
    @endpush

</x-ladmin-panel>
