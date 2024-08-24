<form id="approveQuantity" method="post" action="{{ route('transferReturns.approve') }}">
    @csrf
    <div class="form-group">
        <label for="">Status</label>
        <input type="hidden" name="id" value="{{ encrypt($data->id) }}">
        <select class="form-control" name="status" required>
            <option value="success">Received & Approved</option>
            <option value="pending">Not received</option>
        </select>
    </div>
    <div class="form-group mt-4">
        <button class="btn btn-primary" type="submit" name="Save">Save <i class="ik ik-save"></i></button>
    </div>
</form>