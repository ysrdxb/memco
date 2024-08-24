<div class="row">
    <div class="col-md-12">
        <div class="">
            <div class="">
                <form class="forms-sample" id="submitForm2" method="POST" action="javascript:;">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3>General</h3>
                                    <div class="form-group text-right" style="width:100%;float:right">
                                        <button type="button" class="btn btn-primary saveBtnForTransferWithoutLPO">Save</button>
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
                                            <input type="number" class="form-control" id="transfer_id" name="transfer_no" value="" required>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>Material Required Date <span class="text-red">*</span></label>
                                            <input type="date" class="form-control" name="date" id="transfer_date" value="" required>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label>Remarks</label>
                                            <textarea class="form-control" name="remarks" rows="1"></textarea>
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
