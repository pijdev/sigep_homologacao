<x-ladmin-panel title="Welcome">

    {{-- Setup you content header --}}
    <x-slot name="contentHeader">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold">
                    <i class="bi bi-heart-fill text-danger"></i>
                    Bem-vindo ao SIGEP!
                </h3>
            </div>
        </div>
    </x-slot>

    {{-- Setup you content body --}}
    <div class="row">
        <div class="col-12">
            <i class="bi bi-rocket-takeoff-fill fs-5 text-primary"></i>
            Sistema de Gestão Empresarial v2.0 - Ambiente de Homologação
        </div>
    </div>

    {{-- Push inline scripts if needed --}}
    @push('js')
        <script>
            console.log('SIGEP Homologação está funcionando!');
        </script>
    @endpush

</x-ladmin-panel>
