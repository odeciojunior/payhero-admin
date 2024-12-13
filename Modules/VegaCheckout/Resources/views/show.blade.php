<form id="form_update_integration" method="post" action="#">
    @csrf
    @method('PUT')
    <div class="container-fluid">
        <!-- Integration ID (hidden) -->
        <input type="hidden" id="integration_id" value="" />

        <!-- Token Section -->
        <div class="row mb-3">
            <x-copy-input
                label="Token"
                id="token-edit"
                name="token"
                value=""
                placeholder="Token gerado automaticamente"
                tooltip="Copiar token"
            />
        </div>

        <!-- Webhook Section -->
        <div class="row mb-3">
            <x-copy-input
                label="Webhook"
                id="webhook-edit"
                name="webhook"
                value=""
                placeholder="URL do webhook"
                tooltip="Copiar Webhook"
            />
        </div>

        <!-- X-Signature Section -->
        <div class="row mb-3">
            <x-copy-input
                label="X-Signature"
                id="x-signature-edit"
                name="x-signature"
                value=""
                placeholder="Cabeçalho de Assinatura"
                tooltip="Copiar Cabeçalho Assinatura"
                description="Todas as solicitações enviadas aos seus endpoints de webhook são assinadas para garantir que você
                    possa verificar se o tráfego está realmente vindo da Azcend.
                    Esta assinatura é incluída no X-Signature cabeçalho HTTP, permitindo que o cliente verifique se ela
                    foi criada usando o mesmo segredo."
            />
        </div>
    </div>
</form>

@push('css')
    <link rel="stylesheet" href="{{ mix('build/input-copy-styles.min.css') }}">
@endpush
