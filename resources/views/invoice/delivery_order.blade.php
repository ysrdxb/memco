<!DOCTYPE html>
<html class="no-js" lang="en">

<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <title>Delivery Order | #{{ $transfer->ref_no }}</title>
    <link rel="stylesheet" href="{{ asset('public/invoice/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <style>
        footer {
            background-color: #f2f2f2;
            padding: 20px;
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
            width: 80%;
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            top: 100%;
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
                        <div class="cs-logo cs-mb5 cs-mr20"><img src="{{ asset('public/invoice/img/logo.jpg') }}" alt="Logo"></div>
                    </div>
                    <div class="" style="width:80% !important;">
                        <h6 style="text-align:center;font-weight:600">AL MUHANAD MECH. CONT. LLC</h6>                        
                        <h6 style="text-align:center;font-weight:600">
                        <span style="text-decoration:underline; text-transform:uppercase">Delivery Note</span></h6>
                    </div>
                </div>
                <div class="tm-border-1px cs-mb25"></div>
                <div class="cs-invoice_head  cs-mb25">
                    <div class="cs-invoice_left cs-mr97">
                        <b class="cs-primary_color">Job No: </b>{{ $transfer->delivery ? $transfer->delivery->id : ''}}<br>
                        <b class="cs-primary_color">Job Title: </b>{{ $transfer->store->name }}<br>
                        <b class="cs-primary_color">MR No.: </b>{{ $transfer->id }}<br>
                        <b class="cs-primary_color">MR Date: </b>{{ $transfer->updated_at }}<br>
                    </div>
                    <div class="cs-invoice_right cs-text_right w-33">
                        <b class="cs-primary_color float-left">Delivery No.: </b><span class="float-right">{{ $transfer->delivery ? $transfer->delivery->ref_no : ''}}</span><br>
                        <b class="cs-primary_color float-left">Delivery Date: </b><span class="float-right">{{ $transfer->date }}</span><br>
                        <b class="cs-primary_color float-left">Contact Person: </b><span class="float-right">{{ $transfer->store->users->first()->name }}</span><br>
                        <b class="cs-primary_color float-left">Contact No: </b><span class="float-right">{{ $transfer->store->users->first()->phone }}</span><br>
                        <b class="cs-primary_color float-left">Order No.: </b><span class="float-right">{{ $transfer->delivery ? $transfer->delivery->id : '' }}</span><br>
                    </div>                    
                </div>
                <div class="cs-table cs-style2">
                    <div>
                        <div class="cs-table_responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="cs-semi_bold cs-primary_color">No.</th>
                                        <th class="cs-semi_bold cs-primary_color">Item Description</th>
                                        <th class="cs-semi_bold cs-primary_color">Brand</th>
                                        <th class="cs-semi_bold cs-primary_color">U.O.M</th>
                                        <th class="cs-semi_bold cs-primary_color">Requested QTY</th>                                        
                                        <th class="cs-semi_bold cs-primary_color">Delivered QTY</th>
                                        

                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i =1; @endphp
                                    @foreach($transfer->details as $row)
                                        @if(count($row->product->stockRecords))
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $row->product->name }}</td>
                                            <td>{{ $row->product->brand->name }}</td>   
                                            <td>{{ $row->unit->name }}</td>
                                            <td>{{ $row->requested_quantity }}</td>
                                            <td>{{ $row->delivered_quantity }}</td>
                                                                                     
                                        </tr>
                                        @endif
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
            <p>VERIFIED BY:</p>
            {{ $user->roles->first()->name }} <br>
            {{ $user->name }}
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
   
    <script src="{{ asset('public/invoice/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/invoice/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('public/invoice/js/html2canvas.min.js') }}"></script>
    <script src="{{ asset('public/invoice/js/main.js') }}"></script>
    <script>
       window.onload = function() {
            window.print();
        };        
    </script>
</body>
</html>