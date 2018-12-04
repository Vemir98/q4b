<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Разработчик SUR-SER
 * Компания: WS
 * Дата: 17.05.16
 * Время: 13:47
 * Класс для обмена сообщениями в приложении по средствам сессии
 */

class Message {

    /**
     * Типы сообщений которые можно задать.
     */
    const ERROR   	= 'danger';
    const ALERT 	= 'alert';
    const CRITICAL 	= 'critical';
    const NOTICE  	= 'notice';
    const SUCCESS 	= 'success';
    const WARN    	= 'warning';
    const INFO    	= 'info';
    const ACCESS 	= 'access';
    const DEBUG   	= 'debug';

    /**
     * Псевдонимы для HTML классов
     * @var array
     */
    public static $class_aliases = array(
                                        'danger'     => 'times',
                                        'info'       => 'info',
                                        'success'    => 'check',
                                        'warning'    => 'warning',
                                    );

    /**
     * Сессионный ключ для хранения сообщений
     * @var string
     */
    public static $session_key = 'messages';

    /**
     * вид сообщений по умолчанию
     * @var string
     */
    public static $default_view = 'messages/basic';

    /**
     * Добавление нового сообщения
     *
     * @param   string  $type     Тип сообщения ( Message::SUCCESS)
     * @param   string  $message  Массив/Строка сообщения(й)
     * @param   array   $options  Опции сообщений
     * @return  void
     */
    public static function set($type, $message, array $options = NULL)
    {
        // Подгрузка существующих сообщений
        $messages = (array) self::get();

        // Инициализация сообщений
        if (!is_array($messages))
        {
            $messages = array();
        }

        // Добавить новое сообщение
        if (is_array($message))
        {
            foreach ($message as $_type => $_message)
            {
                $messages[] = (object) array(
                    'type'     => $type,
                    'text'     => $_message,
                    'options'  => (array) $options,
                );
            }
        }
        else
        {
            $messages[] = (object) array(
                'type'     => $type,
                'text'     => $message,
                'options'  => (array) $options,
            );
        }

        // установка сообщений
        Session::instance()->set(self::$session_key, $messages);
    }

    /**
     * Сообщения об ошибке
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function error($message, array $options = NULL)
    {
        self::set(self::ERROR, $message, $options);
    }

    /**
     * Сообщения алерты
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function alert($message, array $options = NULL)
    {
        self::set(self::ALERT, $message, $options);
    }

    /**
     * Критические сообщения
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function critical($message, array $options = NULL)
    {
        self::set(self::CRITICAL, $message, $options);
    }

    /**
     * Сообщения уведомлений
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function notice($message, array $options = NULL)
    {
        self::set(self::NOTICE, $message, $options);
    }

    /**
     * Сообщения об успешности чего либо
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function success($message, array $options = NULL)
    {
        self::set(self::SUCCESS, $message, $options);
    }

    /**
     * Сообщения предупреждение
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function warn($message, array $options = NULL)
    {
        self::set(self::WARN, $message, $options);
    }

    /**
     * Информационные сообщения
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function info($message, array $options = NULL)
    {
        self::set(self::INFO, $message, $options);
    }

    /**
     * Сообщения доступа
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function access($message, array $options = NULL)
    {
        self::set(self::ACCESS, $message, $options);
    }

    /**
     * Sets a debug message, not in production stage.
     *
     * @param	mixed	$message  String/Array for the message(s)
     * @param   array   $options  Any options for the message [Optional]
     */
    public static function debug($message, array $options = NULL)
    {
        if (Kohana::$environment !== Kohana::PRODUCTION)
        {
            self::set(self::DEBUG, $message, $options);
        }
    }

    /**
     * The same as display - used to mold to Kohana standards
     *
     * @param 	mixed 	$type     Message type (e.g. Message::SUCCESS, array(Message::ERROR, Message::ALERT)) [Optional]
     * @param 	bool 	$delete   Delete the messages? [Optional]
     * @param 	mixed 	$view     View filename or View object [Optional]
     *
     * @return	string	HTML for message
     */
    public static function render($type = NULL, $delete = TRUE, $view = NULL)
    {
        return self::display($type, $delete, $view);
    }

    /**
     * Возвращает все сообщения
     *
     *	$messages = Message::get();
     *
     *  	//Get error messages only
     *  	$error_messages = Message::get(Message::ERROR);
     *
     *	// Get error and alert messages
     *  	$messages = Message::get(array(Message::ERROR, Message::ALERT));
     *
     *	// Customize the default value
     *  	$error_messages = Message::get(Message::ERROR, 'No error messages found');
     *
     * @param 	mixed 	$type     Message type (e.g. Message::SUCCESS, array(Message::ERROR, Message::ALERT))
     * @param 	mixed 	$default  Default value to return [Optional]
     * @param 	bool 	$delete   Delete the messages?
     *
     * @return 	mixed 	array or NULL
     */
    public static function get($type = NULL, $default = NULL, $delete = FALSE)
    {
        // Get the messages
        $messages = Session::instance()->get(self::$session_key, array());

        if ($messages === NULL)
        {
            // No messages to return
            return $default;
        }

        if ($type !== NULL)
        {
            // Will hold the filtered set of messages to return
            $return = array();

            // Store the remainder in case delete or get_once is called
            $remainder = array();

            foreach ($messages as $message)
            {
                if (($message['type'] === $type) OR (is_array($type) AND in_array($message['type'], $type)) OR (is_array($type) AND Arr::is_assoc($type) AND !in_array($message['type'], $type[1])))
                {
                    $return[] = $message;
                }
                else
                {
                    $remainder[] = $message;
                }
            }

            // No messages of '$type' found
            if (empty($return))
                return $default;

            $messages = $return;
        }

        if ($delete === TRUE)
        {
            if ($type === NULL OR empty($remainder))
            {
                // Nothing to save, delete the key from memory
                self::clear();
            }
            else
            {
                // Override the messages with the remainder to simulate a deletion
                Session::instance()->set(self::$session_key, $remainder);
            }
        }

        return $messages;
    }

    /**
     * Удаляет сообщения
     *
     * 	Message::clear();
     *
     * 	// Delete error messages
     * 	Message::clear(Message::ERROR);
     *
     * 	// Delete error and alert messages
     * 	Message::clear(array(Message::ERROR, Message::ALERT));
     *
     * @param   mixed  message type (e.g. Message::SUCCESS, array(Message::ERROR, Message::ALERT))
     * @return  void
     */
    public static function clear($type = NULL)
    {
        if ($type === NULL)
        {
            // Delete everything!
            Session::instance()->delete(self::$session_key);
        }
        else
        {
            // Deletion by type happens in get(), too weird?
            self::get($type, NULL, TRUE);
        }
    }

    /**
     * Вывод сообщений
     *
     * @param 	mixed 	$type     Message type (e.g. Message::SUCCESS, array(Message::ERROR, Message::ALERT)) [Optional]
     * @param 	bool 	$delete   Delete the messages? [Optional]
     * @param 	mixed 	$view     View filename or View object [Optional]
     *
     * @return   string   Message to string
     */
    public static function display($type = NULL, $delete = TRUE, $view = NULL)
    {
        $messages = self::get($type, NULL, $delete);
        if (empty($messages))
        {
            // No messages
            return '';
        }

        if ($view === NULL)
        {
            // Use the default view
            $view = self::$default_view;
        }

        // @todo
        if ( ! $view instanceof Kohana_View)
        {
            // Load the view file
            $view = View::make($view);
        }

        return $view->set('messages', $messages)->render();
    }

} 