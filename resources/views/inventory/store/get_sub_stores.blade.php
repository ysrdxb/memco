@if(!empty($subStores))
    <option value="">-Select-</option>
    @foreach($subStores as $row)
        <option value="{{ $row->id }}">{{ $row->name }}</option>
    @endforeach
@else
        <option value="">Sub Store not found!</option>
@endif