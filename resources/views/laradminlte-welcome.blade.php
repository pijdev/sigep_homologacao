<x-ladmin-panel title="Welcome">

    {{-- Setup you content header --}}
    <x-slot name="contentHeader">
        <div class="row">
            <div class="col-12">
                <h3 class="fw-bold">
                    <i class="bi bi-heart-fill text-danger"></i>
                    Welcome to LaradminLTE!
                </h3>
            </div>
        </div>
    </x-slot>

    {{-- Setup you content body --}}
    <div class="row">
        <div class="col-12">
            <i class="bi bi-rocket-takeoff-fill fs-5 text-primary"></i>
            Now, start building your next administration panel with ease and flexibility.
        </div>
    </div>

    {{-- Push inline scripts if needed --}}
    @push('js')
        <script>
            console.log('LaradminLTE is successfully loaded!');
        </script>
    @endpush

</x-ladmin-panel>
