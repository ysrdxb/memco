            <div class="row product-row">
                <div class="form-group col-sm-6">
                    <label><a href="javascript:;" onclick="deleteItem(this)" class="text-danger pr-4">X</a>Select Product <span class="text-red">*</span></label>
                    <select class="form-control select2 product-select" name="product_id[]" onfocus="search(this)" required>
                    <option value="">Select Product</option>
                    </select>
                </div>
                <div class="form-group col-sm-3">
                    <label>Select Unit <span class="text-red">*</span></label>
                    <select class="form-control select2" name="unit_id[]">
                    <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>                
                <div class="form-group col-sm-3">
                    <label>Quantity <span class="text-red">*</span></label>
                    <input type="number" class="form-control quantity" name="quantity[]" required>
                </div>
            </div>