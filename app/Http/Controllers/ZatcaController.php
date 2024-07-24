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
        'TUlJRDRUQ0NBNGFnQXdJQkFnSVRGd0FBTzJVVXE4RmR0Z1lLNWdBQkFBQTdaVEFLQmdncWhrak9QUVFEQWpCaU1SVXdFd1lLQ1pJbWlaUHlMR1FCR1JZRmJHOWpZV3d4RXpBUkJnb0praWFKay9Jc1pBRVpGZ05uYjNZeEZ6QVZCZ29Ka2lhSmsvSXNaQUVaRmdkbGVIUm5ZWHAwTVJzd0dRWURWUVFERXhKUVVscEZTVTVXVDBsRFJWTkRRVEV0UTBFd0hoY05NalF3TVRFMU1UY3lOVEU1V2hjTk1qa3dNVEV6TVRjeU5URTVXakIxTVFzd0NRWURWUVFHRXdKVFFURW1NQ1FHQTFVRUNoTWRUV0Y0YVcxMWJTQlRjR1ZsWkNCVVpXTm9JRk4xY0hCc2VTQk1WRVF4RmpBVUJnTlZCQXNURFZKcGVXRmthQ0JDY21GdVkyZ3hKakFrQmdOVkJBTVRIVlJUVkMwNE9EWTBNekV4TkRVdE16azVPVGs1T1RrNU9UQXdNREF6TUZZd0VBWUhLb1pJemowQ0FRWUZLNEVFQUFvRFFnQUU1VlA5ZFFXbmtpNHpSZ1VZanBORDRBdVZVNFhBVmFxc0piVlR2ZEtJOUVJZHpvSlRGRENXSEdiR0ZOemtZUDB5b05MdG8vNS84MFlEQjV2TkZ1Z1hhS09DQWdrd2dnSUZNSUd2QmdOVkhSRUVnYWN3Z2FTa2dhRXdnWjR4T3pBNUJnTlZCQVFNTWpFdFZGTlVmREl0VkZOVWZETXRaV1F5TW1ZeFpEZ3RaVFpoTWkweE1URTRMVGxpTlRndFpEbGhPR1l4TVdVME5EVm1NUjh3SFFZS0NaSW1pWlB5TEdRQkFRd1BNems1T1RrNU9UazVPVEF3TURBek1RMHdDd1lEVlFRTURBUXhNVEF3TVJFd0R3WURWUVFhREFoU1VsSkVNamt5T1RFY01Cb0dBMVVFRHd3VDJZMVRkWEJ3YkhrZ1lXTjBhWFpwZEdsbGN6QWRCZ05WSFE0RUZnUVVadkJOcHdmMFJzWTBvU2QyWXo2Tjg0aXhCRll3SHdZRFZSMGpCQmd3Rm9BVWNwUFJEbXY2SkZzVGhlckJGZk80RmZzYkJZMHdld1lJS3dZQkJRVUhBUUVFYnpCdE1Hc0dDQ3NHQVFVRkJ6QUNobDlvZEhSd09pOHZZV2xoTVM1NllYUmpZUzVuYjNZdWMyRXZRMlZ5ZEVWdWNtOXNiQzlRVWxwRmFXNTJiMmxqWlZORFFURXVaWGgwWjJGNmRDNW5iM1l1Ykc5allXeGZVRkphUlVsT1ZrOUpRMFZUUTBFeExVTkJLREVwTG1OeWREQU9CZ05WSFE4QkFmOEVCQU1DQjRBd1BBWUpLd1lCQkFHQ054VUhCQzh3TFFZbEt3WUJCQUdDTnhVSWdZYW9IWVRRK3hLRzdaMGtoODc3R2RQQVZXYUgrcVZsaGRtRVBnSUJaQUlCRWpBZEJnTlZIU1VFRmpBVUJnZ3JCZ0VGQlFjREF3WUlLd1lCQlFVSEF3SXdKd1lKS3dZQkJBR0NOeFVLQkJvd0dEQUtCZ2dyQmdFRkJRY0RBekFLQmdnckJnRUZCUWNEQWpBS0JnZ3Foa2pPUFFRREFnTkpBREJHQWlFQS9vaDRIb2FlTGh6SDFNN2YrTjBrSmZoSW42RHlzQkZaWEZNcGdnK3poeG9DSVFDVWwweEtyTGxuZEM5V25QdGVSNUx1dVF2amdQQUpvUklFd2JDeVJpSXk2dz09', // get from ZATCA when you exchange the CSR via APIs
        'MIGEAgEAMBAGByqGSM49AgEGBSuBBAAKBG0wawIBAQQgxcRJQvQGyTiZevMnxRfJ
        KapLnV7D3n/csESeD3+MO/GhRANCAAQJD4a7z4bpBpYW6fackNsyGqAYBP2TLOJ5
        nFCBFyPRqGNLfR5Nqqe4822BM6A0dNotJCOHE5UYoRM5p96Tl3hK' // generated at stage one
    ))->setSecretKey(''); // get from ZATCA when you exchange the CSR via APIs
    
   

    $invoice = (new InvoiceSign($xmlInvoice, $certificate))->sign();
    dd($invoice->getQRCode());
    // invoice Hash: $invoice->getHash()
    // invoice signed as XML: $invoice->getInvoice()
    // Invoice QR code as base64: $invoice->getQRCode()

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
