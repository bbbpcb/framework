<?php
function FileSizeString($i)
{
    return $i >= 1099511627776 ? round($i / 1099511627776, 2) . ' TB' : ($i >= 1073741824 ? round($i / 1073741824, 2) . ' GB' : ($i >= 1048576 ? round($i / 1048576, 2) . ' MB' : ($i >= 1024 ? round($i / 1024, 2) . ' KB' : $i . ' B')));
}
function FileNameFormat($filename)
{
    return rtrim(preg_replace('/[\\/]{2,}/', '/', str_replace('\\', '/', $filename)), '/');
}
function FileCreate($target, $data = null)
{
    $target = FileNameFormat($target);
    if ($data !== null) {
        file_put_contents($target, $data);
        return is_file($target);
    }
    if (is_dir($target)) {
        return true;
    }
    if (!FileDelete($target)) {
        return false;
    }
    return mkdir($target, 511, true);
}
function FileList($target)
{
    $win = substr(PHP_OS, 0, 3) == 'WIN';
    $target = FileNameFormat($target);
    if (is_file($target)) {
        $full = $target;
        $file = basename($full);
        return array(array(is_dir($full), is_writable($full), @filesize($full), @filectime($full), @filemtime($full), $win ? iconv('GBK', 'UTF-8', $file) : $file, base64_encode($full)));
    } elseif (is_dir($target)) {
        $list = array();
        $handle = opendir($target);
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $full = $target . '/' . $file;
            $list[] = array(is_dir($full), is_writable($full), @filesize($full), @filectime($full), @filemtime($full), $win ? iconv('GBK', 'UTF-8', $file) : $file, base64_encode($full));
        }
        closedir($handle);
        return $list;
    } else {
        return array();
    }
}
function FileCopy($source, $target)
{
    $source = FileNameFormat($source);
    $target = FileNameFormat($target);
    if (is_file($source)) {
        return copy($source, $target);
    } elseif (is_dir($source)) {
        if (!file_exists($target)) {
            if (!mkdir($target)) {
                return false;
            }
        } else {
            return false;
        }
        $handle = opendir($source);
        if (!$handle) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_file($source . '/' . $file)) {
                if (!copy($source . '/' . $file, $target . '/' . $file)) {
                    return false;
                }
            } elseif (is_dir($source . '/' . $file)) {
                if (!FileCopy($source . '/' . $file, $target . '/' . $file)) {
                    return false;
                }
            }
        }
        closedir($handle);
        return true;
    } else {
        return false;
    }
}
function FileMove($source, $target)
{
    $source = FileNameFormat($source);
    $target = FileNameFormat($target);
    if (is_file($source)) {
        return rename($source, $target);
    } elseif (is_dir($source)) {
        if (!file_exists($target)) {
            if (!mkdir($target)) {
                return false;
            }
        } else {
            return false;
        }
        $handle = opendir($source);
        if (!$handle) {
            return false;
        }
        while (false !== ($file = readdir($handle))) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            if (is_file($source . '/' . $file)) {
                if (!rename($source . '/' . $file, $target . '/' . $file)) {
                    return false;
                }
            } elseif (is_dir($source . '/' . $file)) {
                if (!FileMove($source . '/' . $file, $target . '/' . $file)) {
                    return false;
                }
            }
        }
        closedir($handle);
        if (!rmdir($source)) {
            return false;
        }
        return true;
    } else {
        return false;
    }
}
function FileDelete($target,$rmdir=true)
{
    $target = FileNameFormat($target);
    if (is_file($target)) {
        if (!is_writable($target)) {
            chmod($target, 438);
        }
        unlink($target);
        return !is_file($target);
    }
    if (!is_dir($target)) {
        return true;
    }
    if (!is_writable($target)) {
        chmod($target, 511);
    }
    $handle = opendir($target);
    while (false !== ($file = readdir($handle))) {
        if ($file == '.' || $file == '..' || $file == '') {
            continue;
        }
        $file = $target . '/' . $file;
        if (is_file($file)) {
            if (!is_writable($file)) {
                chmod($file, 438);
            }
            unlink($file);
            continue;
        }
        FileDelete($file);
    }
    closedir($handle);
    if($rmdir) {
        rmdir($target);
        return !is_dir($target);
    }
    return true;
}
function ZipCompact($zipfile, $files, $root = null)
{
    $tree = array();
    $zip = new ZipArchive();
    if ($zip->open($zipfile, ZipArchive::OVERWRITE) !== TRUE) {
        return $tree;
    }
    foreach (is_array($files) ? $files : array($files) as $file) {
        $file = FileNameFormat($file);
        $path = $root ? FileNameFormat($root) : dirname($file);
        if (strpos($file, $path) !== 0) {
            continue;
        }
        $path .= '/';
        if (is_file($file)) {
            $name = W(substr($file, strlen($path)), basename($file));
            $tree[] = $name;
            $zip->addFile($file, $name);
            continue;
        }
        $task = array(substr($file, strlen($path)));
        while (count($task)) {
            $dir = array_shift($task);
            if ($dir) {
                $dir .= '/';
                $tree[] = $dir;
                $zip->addEmptyDir($dir);
            }
            $handle = opendir($path . $dir);
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_file($path . $dir . $file)) {
                        $tree[] = $dir . $file;
                        $zip->addFile($path . $dir . $file, $dir . $file);
                    } elseif (is_dir($path . $dir . $file)) {
                        $task[] = $dir . $file;
                    }
                }
            }
            closedir($handle);
        }
    }
    $zip->close();
    return $tree;
}
function ZipExtract($zipfile, $path)
{
    $zip = new ZipArchive();
    if ($zip->open($zipfile) !== TRUE) {
        return false;
    }
    $zip->extractTo(FileNameFormat($path) . '/');
    $zip->close();
    return true;
}