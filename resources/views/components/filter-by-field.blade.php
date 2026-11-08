<div>
    <input type="text" name="{{ $term }}" id="{{ $term }}" placeholder="{{ $placeholder }}" class="form-control"
        value="{{ request($term) }}"
        onkeydown="if(event.key === 'Enter'){window.location.href='?{{ $term }}=' + this.value}">
</div>