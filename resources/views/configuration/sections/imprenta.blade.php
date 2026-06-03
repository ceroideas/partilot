@if($printConfiguration ?? null)
    @include('configuration.sections.imprenta-form')
@else
    @include('configuration.sections.imprenta-list')
@endif
