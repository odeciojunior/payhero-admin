@if(app()->isProduction())
    <script
            src="https://browser.sentry-cdn.com/8.32.0/bundle.tracing.min.js"
            integrity="sha384-azPZNenw1tFrK+792hW27tmLnD4rHaeVToH/tzGQEC8vTwaE8DeelviliETYPmdA"
            crossorigin="anonymous"
    ></script>
    <script>
        Sentry.init({
            dsn: '{{ config('sentry.dsn') }}',
            environment: '{{app()->environment()}}',
            integrations: [
                Sentry.browserTracingIntegration()
            ],
            replaysSessionSampleRate: 0.1, // This sets the sample rate at 10%. You may want to change it to 100% while in development and then sample at a lower rate in production.
            replaysOnErrorSampleRate: 1.0 // If you're not already sampling the entire session, change the sample rate to 100% when sampling sessions where errors occur.
        });
    </script>
@endif
