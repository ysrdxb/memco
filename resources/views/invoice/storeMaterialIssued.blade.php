<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="">
    <title>{{ __('Material Requisition') . ' | #' . $data[0]->issue_order_no }}</title>
    <link rel="stylesheet" href="{{ asset('public/invoice/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .cs-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .cs-invoice {
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .cs-invoice .cs-logo {
            margin-bottom: 10px;
        }

        .cs-invoice h6 {
            text-align: center;
            font-weight: 600;
            margin: 0;
        }

        .cs-invoice_head {
            margin-bottom: 20px;
        }

        .cs-invoice_head_table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cs-invoice_head_table th, .cs-invoice_head_table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .cs-invoice_head_table th {
            background-color: #f9f9f9;
        }

        .cs-table {
            margin-bottom: 20px;
        }

        .cs-table_responsive table {
            width: 100%;
            border-collapse: collapse;
        }

        .cs-table_responsive th, .cs-table_responsive td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .cs-semi_bold {
            font-weight: 600;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .footer-column {
            width: 30%;
        }

        .border-bottom-dashed {
            border-bottom: 1px dashed #ddd;
            margin-top: 5px;
        }

        .two-column {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .two-column .column {
            flex: 1;
            min-width: 0;
        }
    </style>
</head>
<body>
    <div class="cs-container">
        <div class="cs-invoice">
            <div class="display-flex justify-content-end">
                <div class="cs-m0 tm-align-item-center cs-invoice_btns tm-align-item-center cs-hide_print cs-p0">
                    <a href="javascript:window.print()" class="cs-invoice_btn cs-p0">
                        <svg class="cs-primary_color" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path d="M384 368h24a40.12 40.12 0 0040-40V168a40.12 40.12 0 00-40-40H104a40.12 40.12 0 00-40 40v160a40.12 40.12 0 0040 40h24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <rect x="128" y="240" width="256" height="208" rx="24.32" ry="24.32" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <path d="M384 128v-24a40.12 40.12 0 00-40-40H168a40.12 40.12 0 00-40 40v24" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32" />
                            <circle cx="392" cy="184" r="24" />
                        </svg>
                        <span class="cs-primary_color">Print</span>
                    </a>
                </div>
            </div>            
            <div class="cs-invoice_in" id="download_section">
                <div class="display-flex space-between cs-type1 border-bottom-none cs-mb10 tm-align-item-center">
                    <div class="cs-logo cs-mb5">
                        <img src="{{ asset('public/invoice/img/logo.jpg') }}" alt="Logo">
                    </div>
                    <div style="text-align:center;">
                        <h6>AL MUHANAD MECH. CONT. LLC</h6>
                        <h6>قسيمة إصدار المواد<br><span style="text-decoration:underline;">Material Issue Voucher</span></h6>
                    </div>
                </div>
                <div class="cs-invoice_head">
                    <div class="two-column">
                        <div class="column">
                            <table class="cs-invoice_head_table">
                                <tbody>
                                    <tr>
                                        <th class="cs-primary_color">Project Name:</th>
                                        <td>{{ $data[0]->project->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="cs-primary_color">Issue Voucher No.:</th>
                                        <td>{{ $data[0]->issue_order_no }}</td>
                                    </tr>
                                    <tr>
                                        <th class="cs-primary_color">Date:</th>
                                        <td>{{ $data[0]->date ?? $data[0]->created_at }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="column">
                            <table class="cs-invoice_head_table">
                                <tbody>
                                    <tr>
                                        <th class="cs-primary_color">Employee Name / Code:</th>
                                        <td>{{ $data[0]->employee->name }} - {{ $data[0]->employee->employee_no }}</td>
                                    </tr>
                                    <tr>
                                        <th class="cs-primary_color">Employee Department:</th>
                                        <td>{{ $data[0]->employee->position->name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="cs-primary_color">Issued By:</th>
                                        <td>{{ ucwords($data[0]->user->name) }}</td>
                                    </tr>
                                    <tr>
                                        <th class="cs-primary_color">Remarks:</th>
                                        <td>{{ $data[0]->remarks }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="cs-table">
                    <div class="cs-table_responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="cs-semi_bold cs-primary_color">#</th>
                                    <th class="cs-semi_bold cs-primary_color">Product Code</th>
                                    <th class="cs-semi_bold cs-primary_color">Product Name</th>
                                    <th class="cs-semi_bold cs-primary_color">Brand</th>
                                    <th class="cs-semi_bold cs-primary_color">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1; @endphp
                                @foreach($data as $row)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $row->product->code }}</td>
                                    <td>{{ $row->product->name }}</td>
                                    <td>{{ $row->brand->name }}</td>
                                    <td>{{ number_format($row->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    </div>
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
