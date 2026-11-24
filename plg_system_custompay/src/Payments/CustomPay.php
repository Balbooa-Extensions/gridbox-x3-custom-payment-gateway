<?php
namespace CustomPay\Plugin\System\CustomPay\Payments;

\defined('_JEXEC') or die;

use Balbooa\Component\Gridbox\Site\Exceptions\PaymentException;
use Balbooa\Component\Gridbox\Site\Payments\Contracts\BasePayment;

final class CustomPay extends BasePayment
{
    public const TYPE = 'custompay';

    protected string $type = self::TYPE;
    protected array $requiredSettings = [
        'secret_key' => 'Secret Key',
    ];

    public function onSubmit(): void
    {
        $payload = json_encode([
            'amount'       => $this->getOrderAmount(),
            'currency'     => $this->currency->code,
            'description'  => $this->getDescription(),
            'callback_url' => $this->getWebhookUrl(),
            'return_url'   => $this->getRedirectUrl(),
            'reference'    => (string) $this->order->id,
        ]);

        $response = $this->request($this->apiUrl('/payments'), $payload, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->getSettingValue('secret_key'),
        ]);

        if (empty($response->id) || empty($response->checkout_url)) {
            throw new PaymentException('CustomPay: payment was not created', 502);
        }

        // The provider's payment ID is how the webhook finds the order.
        $this->updateOrderParams((string) $response->id);
        header('Location: ' . $response->checkout_url);
    }

    public function onWebHook(): void
    {
        $raw = (string) file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_CUSTOMPAY_SIGNATURE'] ?? '';
        $expected = hash_hmac(
            'sha256',
            $raw,
            (string) $this->getSettingValue('secret_key')
        );

        if (!hash_equals($expected, $signature)) {
            http_response_code(400);
            return;
        }

        $event = json_decode($raw);

        // Ignore gridbox_order_id from the URL; look up the unpaid order by payment ID.
        $this->order = null;
        $this->getOrderByParams((string) ($event->payment_id ?? ''));

        if (!$this->order) {
            echo 'OK'; // already processed or unknown
            return;
        }

        if ((string) $event->amount !== $this->getOrderAmount()
            || $event->currency !== $this->currency->code) {
            http_response_code(400);
            return;
        }

        if ($event->status === 'paid') {
            $this->approveOrder($raw);
        } elseif (in_array($event->status, ['failed', 'cancelled'], true)) {
            $this->cancelOrder();
        }

        echo 'OK';
    }

    public function onAfterRedirect(): void
    {
        // The webhook sets the status; the buyer's return proves nothing.
    }

    private function apiUrl(string $path): string
    {
        $sandbox = ($this->settings->params->environment ?? 'production') === 'sandbox';

        return ($sandbox ? 'https://sandbox.custompay.example' : 'https://api.custompay.example') . $path;
    }
}
