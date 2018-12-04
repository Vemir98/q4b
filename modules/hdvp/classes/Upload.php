<?php

/**
 * Created by PhpStorm.
 * User: SUR0
 * Date: 05.10.2016
 * Time: 22:57
 */
class Upload extends Kohana_Upload
{
    /**
     * Save an uploaded file to a new location. If no filename is provided,
     * the original filename will be used, with a unique prefix added.
     *
     * This method should be used after validating the $_FILES array:
     *
     *     if ($array->check())
     *     {
     *         // Upload is valid, save it
     *         Upload::save($array['file']);
     *     }
     *
     * @param   array   $file       uploaded file data
     * @param   string  $filename   new filename
     * @param   string  $directory  new directory
     * @param   integer $chmod      chmod mask
     * @return  string  on success, full path to new file
     * @return  FALSE   on failure
     */
    public static function save(array $file, $filename = NULL, $directory = NULL, $chmod = 0644)
    {
        if ( ! isset($file['tmp_name']) OR ! is_uploaded_file($file['tmp_name']))
        {
            // Ignore corrupted uploads
            return FALSE;
        }

        if ($filename === NULL)
        {
            // Use the default filename, with a timestamp pre-pended
            $namePaths = explode('.',$file['name']);
            $ext = end($namePaths);
            if(empty($ext)){
                $ext = File::ext_by_mime(File::mime($file['tmp_name']));
            }

            $filename = uniqid().'.'.$ext;
        }else{
            $filename = iconv("utf-8", "cp936", $filename);
        }

        if (Upload::$remove_spaces === TRUE)
        {
            // Remove spaces from the filename
            $filename = preg_replace('/\s+/u', '_', $filename);
        }

        if ($directory === NULL)
        {
            // Use the pre-configured upload directory
            $directory = Upload::$default_directory;
        }

        if ( ! is_dir($directory) OR ! is_writable(realpath($directory)))
        {
            throw new Kohana_Exception('Directory :dir must be writable',
                array(':dir' => Debug::path($directory)));
        }

        // Make the filename into a complete path
        $filename = $directory.DIRECTORY_SEPARATOR.$filename;

        if (move_uploaded_file($file['tmp_name'], $filename))
        {
            if ($chmod !== FALSE)
            {
                // Set permissions on filename
                chmod($filename, $chmod);
            }

            // Return new file path
            return $filename;
        }

        return FALSE;
    }

    public static function signature(array $file)
    {
        if ($file['error'] !== UPLOAD_ERR_OK)
            return TRUE;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        return Valid::check_file_signature($file['tmp_name'],strtolower($ext));
    }
}