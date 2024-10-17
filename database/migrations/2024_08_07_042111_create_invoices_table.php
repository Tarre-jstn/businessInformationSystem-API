 <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {

        //THIS DATABASE TABLE FOLLOWS RECEIPT PRINTING FORMAT 

            //FOR RETRIEVAL OF BUSINESS INFORMATION 
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('business_id')->on('businesses')->onDelete('cascade');
            
            $table->string('business_Name');
            $table->string('business_Address');
            $table->string('business_TIN');
             
            
        
            //FOR INVOICE INFORMATION 
            $table->id('invoice_system_id')->primary(); //MAIN ID IN THE SYSTEM
            $table->unsignedBigInteger('invoice_id'); //INVOICE ID FOR INPUTTING INVOICE
            $table->date('date'); 
            $table->string('terms')->nullable(); 
            $table->enum('status', ['paid', 'partially paid', 'unpaid', 'pending refund', 'refunded']); //if receipt is paid, unpaid, or partially paid.

            $table->string('authorized_Representative')->nullable(); //cashier or authorized representative: EMPLOYEE THAT MADE THE INVOICE.
            $table->enum('payment_Type', ['cash','check', 'online transaction']); //will customer pay through cash or check


            //FOR CUSTOMER INFO IN INVOICE
            $table->string('customer_Name')->nullable();
            $table->string('customer_Address')->nullable();
            $table->bigInteger('customer_TIN')->nullable(); //BIR TIN
            $table->string('customer_Business_Style')->nullable(); 
            $table->string('customer_PO_No')->nullable(); //Post Office No.
            $table->string('customer_OSCA_PWD_ID_No')->nullable(); //PWD or Senior No.

            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            //TO STORE MULTIPLE ITEMS IN ONE TABLE 
            $table->id();
            $table->unsignedBigInteger('invoice_system_id');
            $table->foreign('invoice_system_id')->references('invoice_system_id')->on('invoices')->onDelete('cascade');

            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            $table->string('product_name')->nullable(); // name
            $table->decimal('product_price', 8, 2)->nullable(); // price - price per unit
            $table->enum('seniorPWD_discountable', ['yes','no'])->default('no'); //seniorPWD_discountable
            $table->enum('on_sale', ['yes','no'])->default('no');
            $table->decimal('on_sale_price', 8, 2)->default(0)->nullable(); //on_sale_price
            
            $table->decimal('final_price', 8, 2)->default(0)->nullable();//computed after every discounts are computed

            $table->bigInteger('quantity')->nullable();
            $table->decimal('amount', 8, 2)->nullable(); // calculated as product_price * quantity

            // //SENIOR DISCOUNTABLE
            // $table->

            // //FINAL AMOUNT
            // $table->

            $table->timestamps();
        });

        Schema::create('invoice_additionals', function (Blueprint $table) {
            //TO STORE MULTIPLE ITEMS IN ONE TABLE 
            $table->id();
            $table->unsignedBigInteger('invoice_system_id');
            $table->foreign('invoice_system_id')->references('invoice_system_id')->on('invoices')->onDelete('cascade');

            //FOR ADDITIONAL DESCRIPTION INFO (ADDITIONAL COSTS/SERVCES)
            $table->string('addtl_Costs_Description')->nullable(); //abbreviated to aCD
            $table->string('aCD_quantity')->nullable(); //abbreviated to aCD
            $table->string('aCD_amount')->nullable(); //abbreviated to aCD
            $table->decimal('aCD_Total_Amount', 8, 2)->nullable(); //price of additional price/services

            // //SENIOR DISCOUNTABLE
            // $table->

            // //FINAL AMOUNT
            // $table->

            $table->timestamps();
        });

        Schema::create('invoice_computations', function (Blueprint $table) {
            //TO STORE MULTIPLE ITEMS IN ONE TABLE 
            $table->id();
            $table->unsignedBigInteger('invoice_system_id');
            $table->foreign('invoice_system_id')->references('invoice_system_id')->on('invoices')->onDelete('cascade');

            //FOR STORING VAT, DISCOUNTS, ETC. (WITH COMPUTATIONS)
            $table->decimal('VATable_Sales', 8, 2)->nullable(); // VATable_Sales = VAT_Inclusive / 1.12
            $table->decimal('VAT_Exempt_Sales', 8, 2)->nullable(); // VAT_Exempt_Sales = VAT_Inclusive / 1.12
            $table->decimal('Zero_Rated_Sales', 8, 2)->nullable();
            $table->decimal('VAT_Amount', 8, 2)->nullable(); //VAT_Amount = Amoun_NET_of_VAT * 0.2

            $table->decimal('VAT_Inclusive', 8, 2)->nullable(); // VAT_Inclusive = Amount + Amount...
            $table->decimal('Less_VAT', 8, 2)->nullable(); // Less_VAT = Amoun_NET_of_VAT * 0.2
            $table->decimal('Amount_NET_of_VAT', 8, 2)->nullable(); // VAT_Inclusive / 1.12
            $table->enum('senior_PWD_discountable', ['yes','no'])->default('no'); //seniorPWD_discountable
            $table->decimal('Less_SC_PWD_Discount', 8, 2)->nullable(); //WILL BE COMPUTED ON RECEIPT FRONTEND (includes VAT_Exempt_Sales)
            $table->decimal('Less_SC_PWD_Discount_Percent', 8, 2)->nullable();
            $table->decimal('Amount_Due', 8, 2)->nullable(); // Amount_Due = VAT_Inclusive - (VATable_Sales * 0.02)
            $table->decimal('Add_VAT', 8, 2)->nullable(); // Add_VAT = VAT_Inclusive - VATable_Sales

            /*
                NOTES:
                    VATable_Sales = VAT_Inclusive / 1.12
                    VAT_Amount = Amoun_NET_of_VAT * 0.2
                    VAT_Inclusive = Amount + Amount...

                    VATable_Sales == Amount_NET_of_VAT
                    VAT_Amount == Less_VAT == Add_VAT
                    VAT_Inclusive == Total_Amount_Due

                    VAT_Exempt_Sales, Zero_Rated_Sales, and Less_SC_PWD_Discount are for DISCOUNTS
                    VAT_Exempt_Sales wont have value if VAT IS NOT INCLUDED on Senior Discount

                ORDER OF COMPUTATION:
                    VAT_Inclusive -> Amount_NET_of_VAT -> LESS VAT -> Amount_Due -> Senior_Discount -> Total_Amount_Due
            */
            

            //FOR STORING TOTAL AMOUNT DUE (FINAL COMPUTATION)
            $table->decimal('tax',8,2);
            $table->decimal('total_Amount_Due', 8, 2);

            $table->timestamps();

        });

    }






    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoice_additionals');
        Schema::dropIfExists('invoice_computations');
    }
}; 

