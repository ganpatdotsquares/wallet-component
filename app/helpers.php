<?php // Code within app\Helpers\Helper.php
  use App\Models\Setting;

  if (! function_exists('mainlogofavicon')) {

    function mainlogofavicon()
    {
        $mainLogo = Setting::where('slug','main_logo')->first();
        $favicon = Setting::where('slug','main_favicon')->first();;
        return array('mainLogo'=>$mainLogo,'favicon'=>$favicon);
    }
  }

  // Helper method to convert file size
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2)
    {
        if ($bytes == 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

// one drive helper for convert number to letter
if (!function_exists('getExcelColumnLetter')) {
    function getExcelColumnLetter($number)
    {
        $letter = '';
        while ($number > 0) {
            $mod = ($number - 1) % 26;
            $letter = chr(65 + $mod) . $letter;
            $number = (int)(($number - $mod) / 26);
        }
        return $letter;
    }
}
