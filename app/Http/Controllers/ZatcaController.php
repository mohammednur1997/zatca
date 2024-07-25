<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Salla\ZATCA\GenerateCSR;
use Salla\ZATCA\Models\CSRRequest;

use Salla\ZATCA\Helpers\Certificate;
use Salla\ZATCA\Models\InvoiceSign;

use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ZatcaController extends Controller
{
    //

     public function generate_csr(Request $request){

     $data = CSRRequest::make()
                        ->setUID("311695108900003")
                        ->setSerialNumber('1-TST', '2-TST', 'ed22f1d8-e6a2-1118-9b58-d9a8f11e445f')
                        ->setCommonName('شركة تزامن للخدمات التسويقية شركة شخص واحد')
                        ->setCountryName('SA')
                        ->setOrganizationName('شركة تزامن للخدمات التسويقية شركة شخص واحد')
                        ->setOrganizationalUnitName('makkah branch')
                        ->setRegisteredAddress('مكة المكرمة،حي الجامعة،طريق المسجد الحرام، 24243')
                        ->setInvoiceType(true, true) //invoice types , the default is true, true
                        ->setCurrentZatcaEnv('simulation') //support all modes ['sandbox','simulation','core']
                        ->setBusinessCategory('information company');

    $CSR = GenerateCSR::fromRequest($data)->initialize()->generate();

  //  return "sdfsdf";

    // writing the private_key to file
    openssl_pkey_export_to_file($CSR->getPrivateKey(), 'private_key');

    //writing the csr_content to file
    file_put_contents('content', $CSR->getCsrContent());

}

 public function signing_invoice(Request $request){

    $xmlInvoice = file_get_contents(__DIR__ . '/invoice/simplified_invoice.xml');



    $certificate = (new Certificate(
        'TUlJQzhqQ0NBcG1nQXdJQkFnSUdBWkRwaU1Ydk1Bb0dDQ3FHU000OUJBTUNNQlV4RXpBUkJnTlZCQU1NQ21WSmJuWnZhV05wYm1jd0hoY05NalF3TnpJMU1UQTFOakE0V2hjTk1qa3dOekkwTWpFd01EQXdXakNCMkRFTE1Ba0dBMVVFQmhNQ1UwRXhGakFVQmdOVkJBc01EVzFoYTJ0aGFDQmljbUZ1WTJneFZ6QlZCZ05WQkFvTVR0aTAyTEhaZzlpcElOaXEyTExZcDltRjJZWWcyWVRaaE5pdTJLL1poZGluMktvZzJLZlpoTmlxMkxQWmlObUsyWUxaaXRpcElOaTAyTEhaZzlpcElOaTAySzdZdFNEWmlOaW4ySzNZcnpGWU1GWUdBMVVFQXd4UDJMVFlzZG1EMktrZzJLcllzdGluMllYWmhpRFpoTm1FMks3WXI5bUYyS2ZZcWlEWXA5bUUyS3JZczltSTJZclpndG1LMktrZzJMVFlzZG1EMktrZzJMVFlydGkxSU5tSTJLZllyZGl2Q1RCV01CQUdCeXFHU000OUFnRUdCU3VCQkFBS0EwSUFCRHZxWjFOZXJVdFV5ekxNbTRNUzV3cFJiMUd6MllLRUl5YWE2TGNSSHM5OWUxaWFVcC9DT3FMUExHcXhOY1JJZXdNRkt0R01WUzhMZzczY0hMUzVsdWVqZ2dFU01JSUJEakFNQmdOVkhSTUJBZjhFQWpBQU1JSDlCZ05WSFJFRWdmVXdnZktrZ2U4d2dld3hPekE1QmdOVkJBUU1NakV0VkZOVWZESXRWRk5VZkRNdFpXUXlNbVl4WkRndFpUWmhNaTB4TVRFNExUbGlOVGd0WkRsaE9HWXhNV1UwTkRWb01SOHdIUVlLQ1pJbWlaUHlMR1FCQVF3UE16RXhOamsxTVRBNE9UQXdNREF6TVEwd0N3WURWUVFNREFReE1UQXdNVjh3WFFZRFZRUWFERmJaaGRtRDJLa2cyS2ZaaE5tRjJZUFlzZG1GMktuWWpOaXQyWW9nMktmWmhOaXMyS2ZaaGRpNTJLbllqTmkzMkxIWml0bUNJTmluMllUWmhkaXoyS3pZcnlEWXA5bUUySzNZc2RpbjJZWFlqQ0F5TkRJME16RWNNQm9HQTFVRUR3d1RhVzVtYjNKdFlYUnBiMjRnWTI5dGNHRnVlVEFLQmdncWhrak9QUVFEQWdOSEFEQkVBaUJkWTU3SW1jSTRsd3BHT3lPdVhqenZVVHdEbHRUVU8vMDNDWVQwcUQ3bEFRSWdEb04ya1RGVmNCWXhKT3hFc2NLVEY3VXNyZ2psWlE2OVNES1VwMGcyU0p3PQ==', // get from ZATCA when you exchange the CSR via APIs
        'MIGEAgEAMBAGByqGSM49AgEGBSuBBAAKBG0wawIBAQQgxcRJQvQGyTiZevMnxRfJ
        KapLnV7D3n/csESeD3+MO/GhRANCAAQJD4a7z4bpBpYW6fackNsyGqAYBP2TLOJ5
        nFCBFyPRqGNLfR5Nqqe4822BM6A0dNotJCOHE5UYoRM5p96Tl3hK' // generated at stage one
    ))->setSecretKey('2498x0snO8oK2rw3lwoqKQIMCXYbHSzwOvbh/S9srYo='); // get from ZATCA when you exchange the CSR via APIs
    
   

    $invoice = (new InvoiceSign($xmlInvoice, $certificate))->sign();

    // invoice Hash: $invoice->getHash()
    // invoice signed as XML: $invoice->getInvoice()
    // Invoice QR code as base64: $invoice->getQRCode()

    $response = Http::withHeaders([
        'accept-language' => 'en',
        'Clearance-Status' => "1",
        'Accept-Version' => "v2"
    ])->withBasicAuth(
        'TUlJQzhqQ0NBcG1nQXdJQkFnSUdBWkRwaU1Ydk1Bb0dDQ3FHU000OUJBTUNNQlV4RXpBUkJnTlZCQU1NQ21WSmJuWnZhV05wYm1jd0hoY05NalF3TnpJMU1UQTFOakE0V2hjTk1qa3dOekkwTWpFd01EQXdXakNCMkRFTE1Ba0dBMVVFQmhNQ1UwRXhGakFVQmdOVkJBc01EVzFoYTJ0aGFDQmljbUZ1WTJneFZ6QlZCZ05WQkFvTVR0aTAyTEhaZzlpcElOaXEyTExZcDltRjJZWWcyWVRaaE5pdTJLL1poZGluMktvZzJLZlpoTmlxMkxQWmlObUsyWUxaaXRpcElOaTAyTEhaZzlpcElOaTAySzdZdFNEWmlOaW4ySzNZcnpGWU1GWUdBMVVFQXd4UDJMVFlzZG1EMktrZzJLcllzdGluMllYWmhpRFpoTm1FMks3WXI5bUYyS2ZZcWlEWXA5bUUyS3JZczltSTJZclpndG1LMktrZzJMVFlzZG1EMktrZzJMVFlydGkxSU5tSTJLZllyZGl2Q1RCV01CQUdCeXFHU000OUFnRUdCU3VCQkFBS0EwSUFCRHZxWjFOZXJVdFV5ekxNbTRNUzV3cFJiMUd6MllLRUl5YWE2TGNSSHM5OWUxaWFVcC9DT3FMUExHcXhOY1JJZXdNRkt0R01WUzhMZzczY0hMUzVsdWVqZ2dFU01JSUJEakFNQmdOVkhSTUJBZjhFQWpBQU1JSDlCZ05WSFJFRWdmVXdnZktrZ2U4d2dld3hPekE1QmdOVkJBUU1NakV0VkZOVWZESXRWRk5VZkRNdFpXUXlNbVl4WkRndFpUWmhNaTB4TVRFNExUbGlOVGd0WkRsaE9HWXhNV1UwTkRWb01SOHdIUVlLQ1pJbWlaUHlMR1FCQVF3UE16RXhOamsxTVRBNE9UQXdNREF6TVEwd0N3WURWUVFNREFReE1UQXdNVjh3WFFZRFZRUWFERmJaaGRtRDJLa2cyS2ZaaE5tRjJZUFlzZG1GMktuWWpOaXQyWW9nMktmWmhOaXMyS2ZaaGRpNTJLbllqTmkzMkxIWml0bUNJTmluMllUWmhkaXoyS3pZcnlEWXA5bUUySzNZc2RpbjJZWFlqQ0F5TkRJME16RWNNQm9HQTFVRUR3d1RhVzVtYjNKdFlYUnBiMjRnWTI5dGNHRnVlVEFLQmdncWhrak9QUVFEQWdOSEFEQkVBaUJkWTU3SW1jSTRsd3BHT3lPdVhqenZVVHdEbHRUVU8vMDNDWVQwcUQ3bEFRSWdEb04ya1RGVmNCWXhKT3hFc2NLVEY3VXNyZ2psWlE2OVNES1VwMGcyU0p3PQ==', 
        '2498x0snO8oK2rw3lwoqKQIMCXYbHSzwOvbh/S9srYo='
        )->post('https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal/invoices/reporting/single', [
        'invoiceHash' => $invoice->getHash(),
        'uuid' => '8e6000cf-1a98-4174-b3e7-b5d5954bc10d',
        'invoice' => base64_encode($invoice->getInvoice())
    ]);

    dd(json_decode($response->body()));
 }

 public function signing_invoice_osama(){

    $xmlInvoice = file_get_contents(__DIR__ . '/invoice/simplified_invoice.xml');
    $content = file_get_contents(public_path( 'content'));
    $private_key = file_get_contents(public_path( 'private_key'));
//return dd($content)

     $certificate = (new Certificate(
         'MIID6zCCA5CgAwIBAgITbwAAgLTUs0JsZqZVAQABAACAtDAKBggqhkjOPQQDAjBjMRUwEwYKCZImiZPyLGQBGRYFbG9jYWwxEzARBgoJkiaJk/IsZAEZFgNnb3YxFzAVBgoJkiaJk/IsZAEZFgdleHRnYXp0MRwwGgYDVQQDExNUU1pFSU5WT0lDRS1TdWJDQS0xMB4XDTIyMTAwNjEyNTcyNloXDTI0MTAwNTEyNTcyNlowTjELMAkGA1UEBhMCU0ExEzARBgNVBAoTCjM5OTk5OTk5OTkxDDAKBgNVBAsTA1RTVDEcMBoGA1UEAxMTVFNULTM5OTk5OTk5OTkwMDAwMzBWMBAGByqGSM49AgEGBSuBBAAKA0IABGGDDKDmhWAITDv7LXqLX2cmr6+qddUkpcLCvWs5rC2O29W/hS4ajAK4Qdnahym6MaijX75Cg3j4aao7ouYXJ9GjggI5MIICNTCBmgYDVR0RBIGSMIGPpIGMMIGJMTswOQYDVQQEDDIxLVRTVHwyLVRTVHwzLTA3MzBlZThlLTA4OWQtNDQ1OS1hMzg3LWIxMTg5NGJmMTQyOTEfMB0GCgmSJomT8ixkAQEMDzM5OTk5OTk5OTkwMDAwMzENMAsGA1UEDAwEMTEwMDEMMAoGA1UEGgwDVFNUMQwwCgYDVQQPDANUU1QwHQYDVR0OBBYEFDuWYlOzWpFN3no1WtyNktQdrA8JMB8GA1UdIwQYMBaAFHZgjPsGoKxnVzWdz5qspyuZNbUvME4GA1UdHwRHMEUwQ6BBoD+GPWh0dHA6Ly90c3RjcmwuemF0Y2EuZ292LnNhL0NlcnRFbnJvbGwvVFNaRUlOVk9JQ0UtU3ViQ0EtMS5jcmwwga0GCCsGAQUFBwEBBIGgMIGdMG4GCCsGAQUFBzABhmJodHRwOi8vdHN0Y3JsLnphdGNhLmdvdi5zYS9DZXJ0RW5yb2xsL1RTWkVpbnZvaWNlU0NBMS5leHRnYXp0Lmdvdi5sb2NhbF9UU1pFSU5WT0lDRS1TdWJDQS0xKDEpLmNydDArBggrBgEFBQcwAYYfaHR0cDovL3RzdGNybC56YXRjYS5nb3Yuc2Evb2NzcDAOBgNVHQ8BAf8EBAMCB4AwHQYDVR0lBBYwFAYIKwYBBQUHAwIGCCsGAQUFBwMDMCcGCSsGAQQBgjcVCgQaMBgwCgYIKwYBBQUHAwIwCgYIKwYBBQUHAwMwCgYIKoZIzj0EAwIDSQAwRgIhAOZ8oJnliPhdWvCiokPmStz2niL+1Rbw6y9asAh229z7AiEA0r6l1qnq6vzRjVvr9Hnbtq/9Aki0R4rF64EFNY4XACM=',
//         $content,
         $private_key
     )); // get from ZATCA when you exchange the CSR via APIs



    $invoice = (new InvoiceSign($xmlInvoice, $certificate))->sign();

   // dd($invoice->getInvoice());

  //  dd($invoice->getQRCode());
    // invoice Hash: $invoice->getHash()
    // invoice signed as XML: $invoice->getInvoice()
    // Invoice QR code as base64: $invoice->getQRCode()


    $response = Http::withHeaders([
        'accept-language' => 'en',
        'Clearance-Status' => "1",
        'Accept-Version' => "v2"
    ])->withBasicAuth(
        'TUlJQzh6Q0NBcG1nQXdJQkFnSUdBWkRwbnQ4K01Bb0dDQ3FHU000OUJBTUNNQlV4RXpBUkJnTlZCQU1NQ21WSmJuWnZhV05wYm1jd0hoY05NalF3TnpJMU1URXlNREUyV2hjTk1qa3dOekkwTWpFd01EQXdXakNCMkRFTE1Ba0dBMVVFQmhNQ1UwRXhGakFVQmdOVkJBc01EVzFoYTJ0aGFDQmljbUZ1WTJneFZ6QlZCZ05WQkFvTVR0aTAyTEhaZzlpcElOaXEyTExZcDltRjJZWWcyWVRaaE5pdTJLL1poZGluMktvZzJLZlpoTmlxMkxQWmlObUsyWUxaaXRpcElOaTAyTEhaZzlpcElOaTAySzdZdFNEWmlOaW4ySzNZcnpGWU1GWUdBMVVFQXd4UDJMVFlzZG1EMktrZzJLcllzdGluMllYWmhpRFpoTm1FMks3WXI5bUYyS2ZZcWlEWXA5bUUyS3JZczltSTJZclpndG1LMktrZzJMVFlzZG1EMktrZzJMVFlydGkxSU5tSTJLZllyZGl2Q1RCV01CQUdCeXFHU000OUFnRUdCU3VCQkFBS0EwSUFCTWNLd0JTSENiRGhHWHhPd2tPWktnZnNwOEpVcm1QT2NMN3dZSFNSM3dLOTdJNGVaNFNJVFM2YnZnSmpseDg3V09vS3NtWXZvQktnQkpGL1ZwUWRkeGVqZ2dFU01JSUJEakFNQmdOVkhSTUJBZjhFQWpBQU1JSDlCZ05WSFJFRWdmVXdnZktrZ2U4d2dld3hPekE1QmdOVkJBUU1NakV0VkZOVWZESXRWRk5VZkRNdFpXUXlNbVl4WkRndFpUWmhNaTB4TVRFNExUbGlOVGd0WkRsaE9HWXhNV1UwTkRWb01SOHdIUVlLQ1pJbWlaUHlMR1FCQVF3UE16RXhOamsxTVRBNE9UQXdNREF6TVEwd0N3WURWUVFNREFReE1UQXdNVjh3WFFZRFZRUWFERmJaaGRtRDJLa2cyS2ZaaE5tRjJZUFlzZG1GMktuWWpOaXQyWW9nMktmWmhOaXMyS2ZaaGRpNTJLbllqTmkzMkxIWml0bUNJTmluMllUWmhkaXoyS3pZcnlEWXA5bUUySzNZc2RpbjJZWFlqQ0F5TkRJME16RWNNQm9HQTFVRUR3d1RhVzVtYjNKdFlYUnBiMjRnWTI5dGNHRnVlVEFLQmdncWhrak9QUVFEQWdOSUFEQkZBaUVBeXpCbktUYy84c2JsRE5IMmdhNW1YTHo5aGZvckZzT2FYNUVuejlKMUd1d0NJQjVwTVlvVzVaK3IxTjlQM1Z0RnY2RXFNOUlyM09GVEVpRWM3WEcrTXNzZw==', 
        'mHdk4IUEIyfgwCRVbPHt+d/i6eCEFjkHkVDguMIMhfY='
        )->post('https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal/invoices/reporting/single', [
        'invoiceHash' => $invoice->getHash(),
        'uuid' => '8e6000cf-1a98-4174-b3e7-b5d5954bc10d',
        'invoice' => base64_encode($invoice->getInvoice())
    ]);

    dd(json_decode($response->body()));

 }

 

 public function generate_qr(){
    $generatedString = GenerateQrCode::fromArray([
        new Seller('Salla'), // seller name        
        new TaxNumber('311695108900003'), // seller tax number
        new InvoiceDate('2024-07-12T14:25:09Z'), // invoice date as Zulu ISO8601 @see https://en.wikipedia.org/wiki/ISO_8601
        new InvoiceTotalAmount('100.00'), // invoice total amount
        new InvoiceTaxAmount('15.00') // invoice tax amount
    ])->toBase64();

    dd($generatedString);

 }






}
