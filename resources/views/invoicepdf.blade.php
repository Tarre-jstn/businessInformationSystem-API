<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>A simple, clean, and responsive HTML invoice template</title>

		<style>
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 10px;
				border: 1px solid #eee;
				box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
				font-size: 16px;
				line-height: 24px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

			.invoice-box table tr.top table td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.top table td.title {
				font-size: 45px;
				line-height: 45px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 40px;
			}

			.invoice-box table tr.heading td {
				background: #8B0000;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
                color: #ffffff;
                
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}

            tr.item:nth-child(even) {
            background-color: #fcf2f2;
            }
            tr.item:nth-child(odd) {
                background-color: #ffffff;
            }

		</style>
	</head>

    <body>
    <div class="invoice-box">
        <table style="text-align:center" cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table width="100%">
                        <tr>
                            <!-- Left-aligned content -->
                            <td class="title" style="text-align: left;">
                                <img src="{{ asset('path_to_logo') }}" style="width:120px" />
                            </td>

                            <!-- Empty space between the left and right td -->
                            <td></td>

                            <!-- Right-aligned content -->
                            <td style="text-align: right;">
                                <p style="margin-bottom:0px; font-weight: bold; font-size: 35px">{{$invoice->business_Name}}</p>
                                Address: {{ $invoice->business_Address }}<br />
                                TIN: {{ $invoice->business_TIN ?? 'N/A'}}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4">
                    <table style="line-height:23px"width="100%">
                        <tr>
                            <!-- Left-aligned content -->
                            <td style="width: 50%; text-align: left;">
                                <span style="font-weight: 700;">Customer Name: </span>  <span style="border-bottom: 1px solid black;">{{ $invoice->customer_Name }}</span><br />
                                <span style="font-weight: 700;">Address: </span>        <span style="border-bottom: 1px solid black;">{{ $invoice->customer_Address }}</span><br />
                                <span style="font-weight: 700;">TIN No: </span>         <span style="border-bottom: 1px solid black;">{{ $invoice->customer_TIN }}</span> <br/>
                                <span style="font-weight: 700;">Business Style: </span> <span style="border-bottom: 1px solid black;">{{ $invoice->customer_Business_Style }}</span><br />
                                <span style="font-weight: 700;">Terms: </span>          <span style="border-bottom: 1px solid black;">{{ $invoice->terms }}</span><br />
                            </td>

                            <!-- Empty space between the left and right td -->
                            <td colspan="2"></td>

                            <!-- Right-aligned content -->
                            <td style="width: 50%; text-align: start;">
                                <span style="font-weight: 700;">Payment Type: </span> <span style="border-bottom: 1px solid black;">{{ $invoice->payment_Type }}</span><br />
                                <span style="font-weight: 700;">P.O. Number: </span> <span style="border-bottom: 1px solid black;">{{ $invoice->customer_PO_No }}</span><br />
                                <span style="font-weight: 700;">OSCA/PWD I.D. No.: </span> <span style="border-bottom: 1px solid black;">{{ $invoice->customer_OSCA_PWD_ID_No}}</span><br />
                                <span style="font-weight: 700;">OSCA/PWD Signature: </span> ____________________________<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <table style="margin-top: -15px">
                    <tr>
                        <td style="font-weight: 700; font-size: 23px;"> SALES INVOICE # {{$invoice->invoice_id}}</td>
                        <td style="text-align: right; font-size: 15px;">DATE: {{$invoice->date}}</td>
                    </tr>
                </table>
            </tr>

            <tr>
                <table style="line-height:23px ; text-align:center" cellpadding="0" cellspacing="0">
                    <tr style=" text-align:center;" class="text-align:center heading">
                        <td style="text-align:center; vertical-align: middle; width: 200px;">Description</td>
                        <td style="text-align:center; vertical-align: middle; width: 150px;">Unit Price</td>
                        <td style="text-align:center; vertical-align: middle; width: 50px;">Quantity</td>
                        <td style="width: 200px;">Amount</td>
                    </tr>

                    @php
                        $AmountDue = 0; // Initialize total amount variable                
                    @endphp

                    @foreach($invoice_items as $invoice_item)
                    <tr class="item">
                        <td style="text-align:center; vertical-align: middle;">{{ $invoice_item->product_name }}</td>
                        <td style="text-align:center; vertical-align: middle;">{{ number_format($invoice_item->on_sale_price, 2) }}</td>
                        <td style="text-align:center; vertical-align: middle;">{{ $invoice_item->quantity }}</td>
                        <td style="text-align:center; vertical-align: middle;">
                            @php
                                $itemTotal = $invoice_item->on_sale_price * $invoice_item->quantity;
                                $AmountDue += $itemTotal; // Accumulate total amount
                            @endphp
                            {{ number_format($itemTotal, 2) }}
                        </td>
                    </tr>
                    

                    
                    @endforeach

                    @foreach($invoice_additionals as $invoice_additional)
                    <tr class="item">
                        <td style="text-align:center; vertical-align: middle;">{{ $invoice_additional->addtl_Costs_Description }}</td>
                        <td style="text-align:center; vertical-align: middle;">{{ number_format($invoice_additional->aCD_amount, 2) }}</td>
                        <td style="text-align:center; vertical-align: middle;">{{ $invoice_additional->aCD_quantity }}</td>
                        <td style="text-align:center; vertical-align: middle;">
                            @php
                                $additionalTotal = $invoice_additional->aCD_amount * $invoice_additional->aCD_quantity;
                                $AmountDue += $additionalTotal; // Accumulate total amount
                            @endphp
                            {{ number_format($additionalTotal, 2) }}
                        </td>
                    </tr>
                    @endforeach

                </table>
            </tr>

            <tr>
                <table cellpadding="0" cellspacing="0">
                    <tr class="heading">
                        <td style="width: 200px;" colspan="4"></td> <!-- Empty cells for spacing -->
                        <td style="width: 430px; text-align: right; font-weight: bold; padding-right: 10px">Amount Due</td>
                        <td style="width: 200px; text-align: center;">{{ number_format($AmountDue, 2) }}</td> 
                    </tr>
                </table>
            </tr>

            <tr>
                <table style="line-height: 18px; font-size:15px">
                    <tr>
                        <td style="width: 210px;" colspan="1">
                        <span style="font-weight: 700;">VATable Sales: </span> {{ number_format($invoice_computation->VATable_Sales,2) }}
                        </td> <!-- Empty cells for spacing -->
                        <td style="width: 189px; text-align: right; font-weight: bold; ">
                            <span style="font-weight: 700;">VAT Inclusive:</span>
                        </td>
                        <td style="width: 180px; text-align: center;">{{ number_format($invoice_computation->VAT_Inclusive,2) }}</td> 
                    </tr>
                    <tr>
                        <td style="width: 210px;" colspan="1">
                        <span style="font-weight: 700;">VAT Exempt Sales:</span> {{ number_format($invoice_computation->VAT_Exempt_Sales,2) }}
                        </td> <!-- Empty cells for spacing -->
                        <td style="width: 189px; text-align: right; font-weight: bold; ">
                            <span style="font-weight: 700;">Less VAT:</span>
                        </td>
                        <td style="width: 180px; text-align: center;">{{ number_format($invoice_computation->Less_VAT,2) }}</td> 
                    </tr>
                    <tr>
                        <td style="width: 210px;" colspan="1">
                        <span style="font-weight: 700;">Zero Rated Sales:</span> {{ number_format($invoice_computation->Zero_Rated_Sales,2) }}
                        </td> <!-- Empty cells for spacing -->
                        <td style="width: 189px; text-align: right; font-weight: bold; ">
                            <span style="font-weight: 700;">Amount Due:</span>
                        </td>
                        <td style="width: 180px; text-align: center;">{{ number_format($invoice_computation->Amount_Due,2) }}</td> 
                    </tr>
                    <tr>
                        <td style="width: 210px;" colspan="1">
                        <span style="font-weight: 700;">VAT Amount:</span> {{ number_format($invoice_computation->VAT_Amount,2) }}
                        </td> <!-- Empty cells for spacing -->
                        <td style="width: 189px; text-align: right; font-weight: bold; ">
                            <span style="font-weight: 700;">Add Vat:</span>
                        </td>
                        <td style="width: 180px; text-align: center;">{{ number_format($invoice_computation->Add_VAT,2) }}</td> 
                    </tr>
                    <tr>
                        <td style="width: 210px;" colspan="1">
                        <span style="font-weight: 700;">SC/PWD Discount Percent:</span> {{ number_format($invoice_computation->Less_SC_PWD_Discount_Percent)}}%
                        </td> <!-- Empty cells for spacing -->
                        <td style="width: 189px; text-align: right; font-weight: bold; ">
                            <span style="font-weight: 700;">Less SC/PWD Discount:</span>
                        </td>
                        <td style="width: 180px; text-align: center;">{{ number_format($invoice_computation->Less_SC_PWD_Discount,2) }}</td> 
                    </tr>
                </table>
            </tr>

            <tr>
                <table cellpadding="0" cellspacing="0">
                    <tr class="heading">
                        <td style="width: 200px;" colspan="4"></td> <!-- Empty cells for spacing -->
                        <td style="width: 430px; text-align: right; font-weight: bold; padding-right: 10px">Total Amount Due</td>
                        <td style="width: 200px; text-align: center;">{{ number_format($invoice_computation->total_Amount_Due, 2) }}</td> 
                    </tr>
                </table>
            </tr>


        </table>
        
        <div style="margin-top: 60px; padding-right:20px; text-align: right">
            <span style="border-bottom: 1px solid black;">{{$invoice->authorized_Representative}}</span><br>
            Cashier/Authorized Representative
        </div>

    </div>
</body>
</html>