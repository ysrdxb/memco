<div class="row">
    <div class="col-md-12">
        <div class="">
            <div class="">
                <form class="forms-sample" id="submitForm" method="POST" action="javascript:;">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>General</h3>
                                    <div class="form-group text-right" style="width:100%;float:right">
                                        <button type="button" class="btn btn-primary saveBtn">Save</button>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="row d-none">
                                        <div class="form-group col-sm-12">
                                            <label>Document</label>
                                            <input type="file" class="form-control" name="file" id="file">
                                        </div>
                                    </div>
                                    <h5>MR Details</h5>
                                    <div class="row">
                                        <div class="form-group col-sm-4">
                                            <label>Material Required No. <span class="text-red">*</span></label>
                                            <input type="number" class="form-control" id="material_request_no" name="transfer_no" value="" required>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>Material Required Date <span class="text-red">*</span></label>
                                            <input type="date" class="form-control" name="date" id="material_request_date" value="" required>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>Remarks</label>
                                            <textarea class="form-control" name="remarks" rows="1"></textarea>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5>LPO Details</h5>
                                    <div class="row">

                                        <div class="form-group col-sm-4">
                                            <label>Supplier <span class="text-red"></span></label>
                                            <select class="form-control select2" name="supplier_id">
                                                <option value="">Select</option>
                                                @foreach($suppliers as $supplier)
                                                <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>LPO No. <span class="text-red"></span></label>
                                            <input type="number" class="form-control" id="purchase_no" name="purchase_no" value="">
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>LPO Date <span class="text-red"></span></label>
                                            <input type="date" class="form-control" name="purchase_date" id="purchase_date" value="">
                                        </div>
                                    </div>
                                    <hr>
                                    <h5>Products</h5>

                                    <div id="productRows" class="product-row"></div>
                                    <div class="form-group mt-2">
                                        <a href="javascript:;" class="text-warning add-row" style="border:1px solid;border-radius:25px;padding:3px 21px;">+ Add More Products</a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
