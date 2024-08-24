<!DOCTYPE html>
<html class="no-js" lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <title>Delivery Order | #{{ $data[0]->delivery_order_no }}</title>
    <link rel="stylesheet" href="{{ asset('public/invoice/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <style>
        footer {
            background-color: #f2f2f2;
            padding: 20px;
            position: relative !important;
        }
        .footer-row {
            display: flex;
            justify-content: space-between;
        }
        .footer-column {
            flex: 1;
            text-align: center;
            position: relative;
            padding: 10px;
        }
        .footer-column p {
            margin-bottom: 10px;
            font-weight: bold;
        }
        .footer-column .border-bottom-dashed {
            border-bottom: 1px dashed #000;
            width: 100%;
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            top: 100%;
        }
        .cs-invoice.cs-style1{padding:14px !important}
        .cs-container {
            max-width: 1103px !important;
        }

    </style>  
    <style>
        tr{font-size:12px !important;}
        thead{font-size:12px !important;}
        .cs-primary_color{font-size:12px !important;}
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }        
    </style>    
</head>

<body>
    <div class="cs-container">
        <div class="cs-invoice cs-style1">
            <div class="display-flex justify-content-end">
                <div class="cs-m0 tm-align-item-center cs-invoice_btns tm-align-item-center cs-hide_print cs-p0">                    
                    <a href="javascript:window.print()" id="printBtn" class="cs-invoice_btn cs-p0">
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
                        <div class="cs-logo cs-mb5 cs-mr20"><img src="{{ asset('public/invoice/img/logo.jpg') }}" alt="Logo"></div>
                    </div>
                    <div class="" style="width:80% !important;">
                        <h6 style="text-align:center;font-weight:600">AL MUHANAD MECH. CONT. LLC</h6>                        
                        <h6 style="text-align:center;font-weight:600">
                        <span style="text-decoration:underline; text-transform:uppercase">{{ $data[0]->delivered_by == 'store' ? 'Transfer Voucher' : 'Delivery Note' }}</span></h6>
                    </div>
                </div>
                <div class="tm-border-1px cs-mb25"></div>
                <div class="cs-invoice_head  cs-mb25">
                    <div class="cs-invoice_left cs-mr97">
                        <b class="cs-primary_color">MR No.: </b>{{ $data[0]->transfer->transfer_no }}<br>
                        <b class="cs-primary_color">Purchase No.: </b>{{ $data[0]->purchase ? $data[0]->purchase->purchase_no : 'N/A' }}<br>
                        <b class="cs-primary_color">{{ $data[0]->delivered_by == 'store' ? 'Transfer Voucher No.' :'Delivery Order No.' }} </b>{{ $data[0]->delivered_by == 'store' ? $data[0]->transfer_voucher_id : $data[0]->delivery_order_no }}<br>
                        <b class="cs-primary_color">Project: </b>{{ $data[0]->transfer->requestable->name }}<br>
                    </div>
                    <div class="cs-invoice_right cs-text_right w-33">
                        <b class="cs-primary_color float-left">Delivery Date: </b><span class="float-right">{{ $data[0]->date }}</span><br>
                        <b class="cs-primary_color float-left">Delivery Type: </b><span class="float-right">{{ $data[0]->delivered_by == 'store' ? 'M.T.V' : 'L.P.O' }}</span><br>
                        <b class="cs-primary_color float-left">Requested By: </b><span class="float-right">{{ $data[0]->requestable ? $data[0]->requestable->name : '' }}</span><br>
                        <b class="cs-primary_color float-left">Contact No: </b><span class="float-right">{{ $data[0]->requestable ? $data[0]->requestable->phone : '' }}</span><br>

                    </div>                   
                </div>
                <div class="cs-table cs-style2">
                    <div>
                        <div class="cs-table_responsive">
                            <table class="table table-bordered">
                                <thead>                                   
                                    <tr>
                                        <th class="cs-semi_bold cs-primary_color">No.</th>
                                        <th class="cs-semi_bold cs-primary_color">Product</th>
                                        <th>Serial#</th>
                                        <th class="cs-semi_bold cs-primary_color">Brand</th>
                                        <th class="cs-semi_bold cs-primary_color">U.O.M</th>
                                        <th class="cs-semi_bold cs-primary_color">Req. QTY</th>
                                        <th>Curr. QTY</th>                                                                                
                                        <th>Delvd. QTY</th>
                                        <th>Bal.</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $i => $row)
                                        @php 
                                            $transfer_detail = \App\Models\TransferDetail::where('transfer_id', $row->transfer_id)
                                                ->where('product_id', $row->product_id)
                                                ->first();
                                    
                                            $delivered = $transfer_detail ? $transfer_detail->delivered_quantity : 'N/A';
                                            $balance = $transfer_detail ? $transfer_detail->requested_quantity - $transfer_detail->delivered_quantity : 'N/A';
                                    
                                            $isTool = $row->product->type === 'tool';
                                            $hasSerials = $row->product->print_serials === '1' ? true : false;
                                            $do_serials = $isTool && $hasSerials ? \App\Models\DeliveryOrderSerial::where('delivery_order_id', $row->id)->get() : collect();
                                            $quantity = $row->quantity;
                                        @endphp
                                    
                                        @if($isTool && $hasSerials && $do_serials->isNotEmpty())
                                            @php $quantity = count($do_serials); @endphp
                                        @endif
                                            <tr class="item-break">
                                                <td>{{ $i + 1 }}</td>
                                                <td>{{ $row->product->name }}</td>
                                                <td>
                                                <?php 
                                                    if($row->product->type === 'tool' && $row->product->print_serials === '1') {
                                                       $serials = \App\Models\DeliveryOrderSerial::where('delivery_order_id', $row->id)->get();
                                                       if($serials->isNotEmpty()) {
                                                           foreach($serials as $ser) {
                                                               echo '['.optional($ser->stockDetail)->serial_no.'] ';
                                                           }
                                                       } else {
                                                           echo $row->product->type;
                                                       }
                                                    } else {
                                                        echo $row->product->type;
                                                    }
                                                ?> 
                                                </td>
                                                <td>{{ $row->product->brand->name }}</td>
                                                <td>{{ $row->unit_id ? $row->unit->name : 'N/A' }}</td>
                                                <td>{{ $transfer_detail ? number_format($transfer_detail->requested_quantity, 2) : 'N/A' }}</td>
                                                <td>{{ number_format($quantity, 2) }}</td>
                                                <td>{{ number_format($delivered, 2) }}</td>
                                                <td>{{ number_format($balance, 2) }}</td>
                                                <td>{{ $row->remarks }}</td>
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
    <footer class="page-break">
    <div class="footer-row">
        <div class="footer-column">
            <p>VERIFIED BY:</p>
            M. Khalid <br>
            0582165340
            <div class="border-bottom-dashed"></div>
        </div>
        <div class="footer-column">
            <p>DELIVERED BY <BR> <small>DRIVER NAME:</small> <BR> <small>VEHICLE NO.:</small></p>
            <div class="border-bottom-dashed"></div>
        </div> 
        <div class="footer-column">
            <p>RECEIVED BY <BR><small>NAME:</small><BR><small>MOB NO.:</small></p>
            <div class="border-bottom-dashed"></div>
        </div>                       
    </div>
</footer>
    <button id="printBtn" style="display: none;" onclick="window.print()">Print</button>
   
    <script src="{{ asset('public/invoice/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/invoice/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('public/invoice/js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('public/invoice/js/main.js') }}"></script>
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