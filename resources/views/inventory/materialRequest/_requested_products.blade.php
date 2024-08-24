@foreach($data as $row)
    <option value="{{ $row->product->id }}">{{ $row->product->name }}</option>
@endforeach