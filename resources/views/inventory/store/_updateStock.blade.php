                <form id="submitForms">
                    <div class="form-group">
                        <label for="editName">Product</label>
                        <input type="text" class="form-control" readonly name="product" id="pid" value="{{ $product->name }}">
                        <input type="hidden" value="{{ $product->id }}" name="product_id" id="product_id">
                    </div>
                    <div class="form-group row">
                        <div class="col-md-12">
                            <label for="">Quantity</label>
                            <input type="number" value="{{ $stockRecord ? $stockRecord->quantity : 0 }}" id="pquantity" name="quantity" class="form-control" placeholder="Enter Quantity" required>
                        </div>
                        <div class="col-md-12 col-">
                            <label>Plus / Minus</label>
                            <select class="form-control" name="type">
                                <option value="plus">Plus</option>
                                <option value="minus">Minus</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="editName">D.O Number (if Any)</label>
                        <input type="text" class="form-control" id="delivery_order_no" name="delivery_order_no" value="{{ $stockRecord ?  $stockRecord->delivery_order_no : '' }}">
                    </div>                     
                    <div class="form-group">
                        <label for="editName">M.R Number (if Any)</label>
                        <input type="text" class="form-control" id="material_request_no" name="material_request_no" value="{{ $stockRecord ?  $stockRecord->material_request_no : '' }}">
                    </div>                     
                    <div class="form-group">
                        <label for="editName">Remarks (if Any)</label>
                        <input type="text" class="form-control" id="remarks" name="remarks" value="{{ $stockRecord ?  $stockRecord->remarks : '' }}">
                    </div>                     
                    
                    <div class="form-group mt-4">
                        <button class="btn btn-primary" type="submit" name="Save">Save <i class="ik ik-save"></i></button>
                    </div>
                </form>