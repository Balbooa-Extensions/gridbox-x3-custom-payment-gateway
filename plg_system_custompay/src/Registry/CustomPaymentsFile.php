<?php
namespace CustomPay\Plugin\System\CustomPay\Registry;

\defined('_JEXEC') or die;

final class CustomPaymentsFile
{
    public static function path(): string
    {
        return JPATH_ROOT . '/media/com_gridbox/admin/json/custom-payments.json';
    }

    public static function ensure(string $type, string $title, string $class): void
    {
        $data = self::read();

        if (($data->$type->class ?? null) === $class) {
            return;
        }

        $data->$type = (object) ['title' => $title, 'class' => $class];
        self::write($data);
    }

    public static function remove(string $type): void
    {
        $data = self::read();
        unset($data->$type);
        self::write($data);
    }

    private static function read(): object
    {
        $file = self::path();
        $json = is_file($file) ? json_decode((string) file_get_contents($file)) : null;

        return is_object($json) ? $json : new \stdClass();
    }

    private static function write(object $data): void
    {
        $dir = dirname(self::path());

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(
            self::path(),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            LOCK_EX
        );
    }
}
