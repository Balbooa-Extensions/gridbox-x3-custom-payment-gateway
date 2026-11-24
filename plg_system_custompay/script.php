<?php
\defined('_JEXEC') or die;

use CustomPay\Plugin\System\CustomPay\Registry\CustomPaymentsFile;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        $container->set(InstallerScriptInterface::class, new class () implements InstallerScriptInterface {
            private const TYPE = 'custompay';
            private const TITLE = 'CustomPay';
            private const CLASS_NAME = 'CustomPay\\Plugin\\System\\CustomPay\\Payments\\CustomPay';

            public function install(InstallerAdapter $adapter): bool
            {
                return true;
            }

            public function update(InstallerAdapter $adapter): bool
            {
                return true;
            }

            public function preflight(string $type, InstallerAdapter $adapter): bool
            {
                return true;
            }

            public function postflight(string $type, InstallerAdapter $adapter): bool
            {
                if ($type !== 'install' && $type !== 'update' && $type !== 'discover_install') {
                    return true;
                }

                $this->loadRegistry();
                CustomPaymentsFile::ensure(self::TYPE, self::TITLE, self::CLASS_NAME);

                if ($type !== 'update') {
                    $this->enablePlugin();
                }

                return true;
            }

            public function uninstall(InstallerAdapter $adapter): bool
            {
                $this->loadRegistry();
                CustomPaymentsFile::remove(self::TYPE);

                return true;
            }

            private function loadRegistry(): void
            {
                require_once JPATH_PLUGINS . '/system/custompay/src/Registry/CustomPaymentsFile.php';
            }

            private function enablePlugin(): void
            {
                $db = Factory::getContainer()->get(DatabaseInterface::class);
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__extensions'))
                    ->set($db->quoteName('enabled') . ' = 1')
                    ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
                    ->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
                    ->where($db->quoteName('element') . ' = ' . $db->quote('custompay'));

                $db->setQuery($query)->execute();
            }
        });
    }
};
