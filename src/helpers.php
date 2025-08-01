<?php

if (! function_exists('build_path')) {
    function build_path($segments = [], $leading = false, $url = false): string
    {
        $slash = $url ? '/' : DIRECTORY_SEPARATOR;

        $string = implode($slash, $segments);
        if ($leading) {
            return $slash . $string;
        }

        return $string;
    }
}

if (! function_exists('rmdir_recursive')) {
    function rmdir_recursive($dir): bool
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? rmdir_recursive("$dir/$file") : unlink("$dir/$file");
        }

        return rmdir($dir);
    }
}

if (! function_exists('config_path')) {
    /**
     * Get the configuration path.
     */
    function config_path(string $path = ''): string
    {
        return app()->configPath($path);
    }
}
