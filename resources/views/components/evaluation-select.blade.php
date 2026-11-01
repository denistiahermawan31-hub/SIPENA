<select name="{{ $name }}"
        class="evaluasi"
        data-role="evaluasi"
        @isset($current) data-current="{{ $current }}" @endisset>
    <option value="" @if (empty($current)) selected @endif>— pilih —</option>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @if ($current === $value) selected @endif>{{ $label }}</option>
    @endforeach
</select>
