<?php
/**
 * Created by PhpStorm.
 * User: sur-s
 * Date: 19.05.2021
 * Time: 14:42
 * Обработчик для Put запросов
 * Тут нет обработки только binary
 */

class PutRequestHandler
{
    public static function handle(){
        if(Request::current()->method() === 'PUT'){
            $class = new self();
            if (strpos(Request::current()->headers('content-type'),'multipart/form-data') !== false){
                $class->processFormData();
            }else{
                $class->processRawData();
            }

        }

    }

    private function processRawData(){
        $data = json_decode(file_get_contents("php://input"), true);
        $fields = new ParameterBag($data);
        global $_PUT;
        $_PUT = $GLOBALS['_PUT'] = $fields->all();
    }

    private function processFormData()
    {
        $files = [];
        $data  = [];
        // Получаем контент и определяем границы
        $rawData  = file_get_contents('php://input');
        $boundary = substr($rawData, 0, strpos($rawData, "\r\n"));
        //Получаем и обрабатываем каждую часть
        $parts = $rawData ? array_slice(explode($boundary, $rawData), 1) : [];
        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") {
                break;
            }
            // Отделяем контент от заголовков
            $part = ltrim($part, "\r\n");
            list($rawHeaders, $content) = explode("\r\n\r\n", $part, 2);
            $content = substr($content, 0, strlen($content) - 2);
            // Парсим заголовки
            $rawHeaders = explode("\r\n", $rawHeaders);
            $headers    = array();
            foreach ($rawHeaders as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }
            // Пармис Content-Disposition чтоб получить название поля и тд...
            if (isset($headers['content-disposition'])) {
                $filename = null;
                preg_match(
                    '/^form-data; *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                $fieldName = $matches[1];
                $fileName  = (isset($matches[3]) ? $matches[3] : null);
                // If we have a file, save it. Otherwise, save the data.
                if ($fileName !== null) {
                    $localFileName = tempnam(sys_get_temp_dir(), 'sfy');
                    file_put_contents($localFileName, $content);
//                    $files = $this->normalizeData($files, $fieldName, [
//                        'name'     => $fileName,
//                        'type'     => $headers['content-type'],
//                        'tmp_name' => $localFileName,
//                        'error'    => 0,
//                        'size'     => filesize($localFileName)
//                    ]);
                    $name = $fieldName;
                    $isArray = strpos($fieldName, '[]');
                    if ($isArray && (($isArray + 2) == strlen($fieldName))) {
                        $name = str_replace('[]', '', $fieldName);
                    }

                    $files[$name]['name'][] = $fileName;
                    $files[$name]['type'][] = $headers['content-type'];
                    $files[$name]['tmp_name'][] = $localFileName;
                    $files[$name]['error'][] = 0;
                    $files[$name]['size'][] = filesize($localFileName);

                    // регистрируем shutdown функцию чтоб удалить временные файлы
                    register_shutdown_function(function () use ($localFileName) {
                        unlink($localFileName);
                    });
                } else {
                    $data = $this->normalizeData($data, $fieldName, $content);
                }
            }
        }
        $fields = new ParameterBag($data);
        global $_PUT;
        $_PUT = $GLOBALS['_PUT'] = $fields->all();
        $GLOBALS['_FILES'] = $files;
//        return ["inputs" => $fields->all(), "files" => $files];
    }

    private function normalizeData($data, $name, $value)
    {
        $isArray = strpos($name, '[]');
        if ($isArray && (($isArray + 2) == strlen($name))) {
            $name = str_replace('[]', '', $name);
            $data[$name][]= $value;
        } else {
            $data[$name] = $value;
        }
        return $data;
    }
}