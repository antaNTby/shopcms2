<?php
#####################################
# ShopCMS: Скрипт интернет-магазина
# Copyright (c) by ADGroup
# http://shopcms.ru
#####################################

require_once('core/classes/class.virtual.module.php');

class PaymentModule extends virtualModule {

        function PaymentModule($_ModuleConfigID = 0){

                $this->LanguageDir = 'core/modules/payment/languages/';
                $this->ModuleType = PAYMENT_MODULE;
                $this->MethodsTable = PAYMENT_TYPES_TABLE;
                virtualModule::virtualModule($_ModuleConfigID);
        }


        // *****************************************************************************
        // Purpose        html form to get information from customer about payment,
        //                        this functions does not return <form> </form> tags - these tags are already defined in
        //                        the
        // Inputs
        // Remarks
        // Returns        nothing
        function payment_form_html()
        {
                return "";
        }

        // *****************************************************************************
        // Purpose        core payment processing routine
        // Inputs   $order is array with the following elements:
        //        "customer_email" - customer's email address
        //        "customer_ip" - customer IP address
        //        "order_amount" - total order amount (in conventional units)
        //        "currency_code" - currency ISO 3 code (e.g. USD, GBP, EUR)
        //        "currency_value" - currency exchange rate defined in the backend in 'Configuration' -> 'Currencies' section
        //        "shipping_info" - shipping information - array of the following data:
        //                "first_name", "last_name", "country_name", "state", "city", "address"
        //        "billing_info" - billing information - array of the following data:
        //                "first_name", "last_name", "country_name", "state", "city", "address"
        // Remarks
        function payment_process($order)
        {
                return 1;
        }

        // *****************************************************************************
        // Purpose        PHP code executed after order has been placed
        // Inputs
        // Remarks
        // Returns
        function after_processing_php($orderID)
        {
                return "";
        }

        // *****************************************************************************
        // Purpose        html code printed after order has been placed and after_processing_php
        //                         has been executed
        // Inputs
        // Remarks
        // Returns
        function after_processing_html( $orderID )
        {
                return "";
        }
}
?>