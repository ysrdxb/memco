<!DOCTYPE html>
<html class="no-js" lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <title>{{ __('Material Requisition') . ' | #' . $data->transfer_no }}</title>
    <link rel="stylesheet" href="{{ asset('invoice/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
</head>

<body>
    <div class="cs-container">
        <div class="cs-invoice cs-style1">
            <div class="display-flex justify-content-end">
                <div class="cs-m0 tm-align-item-center cs-invoice_btns tm-align-item-center cs-hide_print cs-p0">
                    <a href="javascript:window.print()" class="cs-invoice_btn cs-p0">
                        <svg class="cs-primary_color" xmlns="http://www.w3.org/2000/svg"
                            class="ionicon cs_primary_color" viewBox="0 0 512 512">
                            <path
                                d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24"
                                fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none"
                                stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none"
                                stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <circle cx="392" cy="184" r="24" />
                        </svg>
                        <span class="cs-primary_color">Print</span>
                    </a>
                </div>
            </div>            
            <div class="cs-invoice_in" id="download_section">
                <div class="display-flex space-between cs-type1 column border-bottom-none cs-mb10 tm-align-item-center">
                    <div class="cs-invoice_left display-flex" style="width:20% !important;">
                        <div class="cs-logo cs-mb5 cs-mr20"><img src="{{ asset('invoice/img/logo.jpg') }}" alt="Logo"></div>
                    </div>
                    <div class="" style="width:80% !important;">
                        <h6 style="text-align:center;font-weight:600">AL MUHANAD MECH. CONT. LLC</h6>                        
                        <h6 style="text-align:center;font-weight:600">
                        طلب المواد<br><span style="text-decoration:underline;">Material Requisition</span></h6>
                    </div>
                </div>
                <div class="tm-border-1px cs-mb25"></div>
                <div class="cs-invoice_head  cs-mb25">
                    <div class="cs-invoice_left cs-mr97">
                        <b class="cs-primary_color">Requisition Code: </b>{{ $data->ref_no }}<br>
                        <b class="cs-primary_color">Requisition No.: </b>{{ $data->transfer_no }}<br>
                        <b class="cs-primary_color">Requisition Date: </b>{{ $data->date }}<br>
                        <b class="cs-primary_color">To: </b>{{ __('Procurement Department') }}<br>
                        <b class="cs-primary_color">From: </b>{{ $data->warehouse->name }}<br>
                        <b class="cs-primary_color">Requested By: </b>{{ $data->user->name }}<br>
                        <b class="cs-primary_color">Remarks: </b>{{ $data->remarks }}<br>
                    </div>
                    <div class="cs-invoice_right cs-text_right">
                        <b class="cs-primary_color">:كود الطلب</b><br>
                        <b class="cs-primary_color">:رقم الطلب</b><br>
                        <b class="cs-primary_color">:تاريخ الطلب</b><br>
                        <b class="cs-primary_color">:ل</b><br>
                        <b class="cs-primary_color">:من</b><br>                        
                        <b class="cs-primary_color">:بتوصية من</b><br>
                        <b class="cs-primary_color">:ملاحظات</b><br>
                    </div>                    
                </div>
                <div class="cs-table cs-style2">
                    <div>
                        <div class="cs-table_responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="cs-semi_bold cs-primary_color">No.</th>
                                        <th class="cs-semi_bold cs-primary_color">Item Code</th>
                                        <th class="cs-semi_bold cs-primary_color">Item Name</th>
                                        <th class="cs-semi_bold cs-primary_color">Brand</th>    
                                        <th class="cs-semi_bold cs-primary_color">U.O.M</th>                                    
                                        <th class="cs-semi_bold cs-primary_color">Requested QTY</th>
                                        <th class="cs-semi_bold cs-primary_color">Delivered QTY</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i =1; @endphp
                                    @foreach($data->details as $row)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $row->product->code }}</td>
                                        <td>{{ $row->product->name }}</td>
                                        <td>{{ $row->product->brand->name }}</td>
                                        <td>{{ $row->unit->name }}</td>
                                        <td>{{ number_format($row->requested_quantity, 2) }}</td>
                                        <td>{{ number_format($row->delivered_quantity, 2) }}</td>
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
<footer>
    <div class="footer-row">
        <div class="footer-column">
            <p>Approved By:</p>
            <div class="border-bottom-dashed"></div>
        </div>
        <div class="footer-column">
            <p>Created By:</p>
            <div class="border-bottom-dashed"></div>
        </div>
        <div class="footer-column">
            <p>Requested By:</p>
            <div class="border-bottom-dashed"></div>
        </div>
    </div>
</footer>
<button id="printBtn" style="display: none;" onclick="window.print()">Print</button>
   
    <script src="{{ asset('/invoice/js/jquery.min.js') }}"></script>
    <script src="{{ asset('/invoice/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('/invoice/js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('/invoice/js/main.js') }}"></script>
    <script>
        // Wait for the document to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Find the print button by its ID
            var printButton = document.getElementById('printBtn');
            // Trigger a click on the print button
            printButton.click();
        });
    </script>    
</body>
</html>