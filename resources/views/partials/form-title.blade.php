@php
    $titleText = $title ?? '';
    $emoji = $emoji ?? '';
    $variant = $variant ?? 'single';
@endphp

<div class="page-title">{!! $emoji ? htmlentities($emoji).' ' : '' !!}{{ $titleText }}</div>

@if($variant === 'single')
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const c = document.querySelector('.form-container');
            if(c) c.classList.add('variant-single');
        });
    </script>
@endif
