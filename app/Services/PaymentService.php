<?php
// app/Services/PaymentService.php
namespace App\Services;

use App\Models\Payment;
use App\Payments\PaymentProcessor;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(protected PaymentProcessor $processor) {}

    public function createPayment(array $data): array
    {
        Log::info('Payment Request Data:', $data);

        $invoice = $data['payable_type']::find($data['payable_id']);
        Log::info('Found Invoice:', [
            'type' => $data['payable_type'],
            'id' => $data['payable_id'],
            'found' => $invoice ? true : false
        ]);
            
        if (!$invoice) {
            throw new \Exception("Invoice not found for type: {$data['payable_type']}, id: {$data['payable_id']}");
        }

        $alreadyPaid=$invoice->payments()->sum('amount');

        if($alreadyPaid>=$invoice->grand_total){
            throw new \Exception("Invoice already paid");
        }


        $payment = Payment::create([
            'payable_type' => $data['payable_type'],
            'payable_id' => $data['payable_id'],
            'amount' => $data['amount'],
            'payment_method_id' => $data['payment_method_id'],
            'status' => 'pending',
        ]);
        
        return $this->processor->pay($payment);
    }


    public function confirmPayment(Payment $payment, array $data): array
    {
        return $this->processor->confirm($payment, $data);
    }

}

