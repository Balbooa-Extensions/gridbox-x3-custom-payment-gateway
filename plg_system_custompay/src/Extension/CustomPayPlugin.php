<?php
namespace CustomPay\Plugin\System\CustomPay\Extension;

\defined('_JEXEC') or die;

use CustomPay\Plugin\System\CustomPay\Payments\CustomPay as Gateway;
use CustomPay\Plugin\System\CustomPay\Registry\CustomPaymentsFile;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class CustomPayPlugin extends CMSPlugin implements SubscriberInterface
{
    /** Insertion point in administrator/components/com_gridbox/tmpl/paymentmethods/default.php */
    private const MARKER = '<h3 class="ba-group-title" data-type="offline">';

    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise' => 'onAfterInitialise',
            'onAfterRender'     => 'onAfterRender',
        ];
    }

    public function onAfterInitialise(): void
    {
        if ($this->getApplication()->getInput()->get('option') !== 'com_gridbox') {
            return;
        }

        CustomPaymentsFile::ensure(Gateway::TYPE, 'CustomPay', Gateway::class);
    }

    public function onAfterRender(): void
    {
        $app = $this->getApplication();
        $input = $app->getInput();

        if (!$app->isClient('administrator')
            || $input->get('option') !== 'com_gridbox'
            || $input->get('view') !== 'paymentmethods') {
            return;
        }

        $body = $app->getBody();

        if (!str_contains($body, self::MARKER) || str_contains($body, 'data-type="custompay"')) {
            return;
        }

        $app->setBody(str_replace(self::MARKER, $this->settingsFields() . self::MARKER, $body));
    }

    private function settingsFields(): string
    {
        return <<<HTML
            <div class="ba-options-group" data-type="custompay">
                <div class="ba-group-element">
                    <label>Secret Key</label>
                    <input type="text" placeholder="Enter value" data-settings="secret_key">
                </div>
            </div>
            <div class="ba-options-group" data-type="custompay">
                <div class="ba-group-element">
                    <label>Environment</label>
                    <select data-settings="environment">
                        <option value="production">Production</option>
                        <option value="sandbox">Sandbox</option>
                    </select>
                </div>
            </div>
        HTML;
    }
}
