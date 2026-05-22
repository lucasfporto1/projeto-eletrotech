
<?php
class Debug
{
    public static function dd($data): void
    {
        echo "<pre style='background: #222; color: #0f0; padding: 20px; border-radius: 5px; margin: 20px; font-size: 14px;'>";
        var_dump($data);
        echo "</pre>";
        die();
    }
}
?>