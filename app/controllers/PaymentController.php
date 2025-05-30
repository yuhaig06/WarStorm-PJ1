<?php

namespace App\Controllers;

class PaymentController
{
    public function processPayment()
    {
        try {
            // Validate request
            if (!isset($_POST['order_id']) || !isset($_POST['payment_method'])) {
                throw new \Exception('Missing required parameters');
            }

            $orderId = $_POST['order_id'];
            $paymentMethod = $_POST['payment_method'];

            // Xử lý logic thanh toán trực tiếp ở đây
            $result = [
                'success' => true,
                'order_id' => $orderId,
                'payment_method' => $paymentMethod,
                'message' => 'Payment processed successfully.'
            ];

            // Return response
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (\Exception $e) {
            // Xử lý lỗi trực tiếp ở đây
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleWebhook()
    {
        try {
            // Get payment method from URL
            $paymentMethod = basename(dirname($_SERVER['REQUEST_URI']));

            // Get raw POST data
            $payload = file_get_contents('php://input');

            // Xử lý logic webhook trực tiếp ở đây
            $result = [
                'success' => true,
                'payment_method' => $paymentMethod,
                'payload' => $payload,
                'message' => 'Webhook handled successfully.'
            ];

            // Return response
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (\Exception $e) {
            // Xử lý lỗi trực tiếp ở đây
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function handleReturn()
    {
        try {
            // Get payment method from URL
            $paymentMethod = basename(dirname($_SERVER['REQUEST_URI']));

            // Xử lý logic return URL dựa trên payment method
            switch ($paymentMethod) {
                case 'paypal':
                    $this->handlePayPalReturn();
                    break;
                case 'momo':
                    $this->handleMomoReturn();
                    break;
                case 'vnpay':
                    $this->handleVNPayReturn();
                    break;
                default:
                    throw new \Exception('Invalid payment method');
            }

            // Redirect to success page
            header('Location: ' . getenv('APP_URL') . '/payment/success');
        } catch (\Exception $e) {
            // Xử lý lỗi trực tiếp ở đây
            // Redirect to error page
            header('Location: ' . getenv('APP_URL') . '/payment/error');
        }
    }

    private function handlePayPalReturn()
    {
        if (!isset($_GET['paymentId']) || !isset($_GET['PayerID'])) {
            throw new \Exception('Missing PayPal parameters');
        }

        // Xử lý logic PayPal return trực tiếp ở đây
        $paymentId = $_GET['paymentId'];
        $payerId = $_GET['PayerID'];

        // TODO: Implement PayPal payment verification
    }

    private function handleMomoReturn()
    {
        if (!isset($_GET['orderId']) || !isset($_GET['resultCode'])) {
            throw new \Exception('Missing MoMo parameters');
        }

        $orderId = $_GET['orderId'];
        $resultCode = $_GET['resultCode'];

        if ($resultCode !== '0') {
            throw new \Exception('MoMo payment failed');
        }

        // Xử lý logic MoMo return trực tiếp ở đây
        // TODO: Implement MoMo payment verification
    }

    private function handleVNPayReturn()
    {
        if (!isset($_GET['vnp_ResponseCode'])) {
            throw new \Exception('Missing VNPay parameters');
        }

        $responseCode = $_GET['vnp_ResponseCode'];

        if ($responseCode !== '00') {
            throw new \Exception('VNPay payment failed');
        }

        // Xử lý logic VNPay return trực tiếp ở đây
        // TODO: Implement VNPay payment verification
    }
}