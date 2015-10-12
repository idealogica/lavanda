<a href="{{ adminRoute(strtolower(class_basename($parms['model'])), 'show', ['id' => $value['id']]) }}">
    {!! !empty($parms['max_len']) ? str_limit($value[$parms['property']], $parms['max_len']) : $value[$parms['property']] !!}
</a>