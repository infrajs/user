<?php

namespace infrajs\user;

use infrajs\template\Template;
use infrajs\mail\Mail;
use infrajs\path\Path;

trait UserMail
{
    //public static $name = 'cart';
    //data - user_id, token, email
    public static function mailtoadmin($user, $lang, $mailroot, $page = false) {
        return static::mail($user, $lang, $mailroot, $page, true);
    }
    public static function mail($user, $lang, $mailroot, $page = false, $admin = false)
    {
        $email = $user['email'];
        $data = $user;
        if ($page) $data['page'] = $page;

        $https = 'http://';
        if (isset($_SERVER['HTTPS'])) $https = 'https://';
        if (isset($_SERVER['REQUEST_SCHEME'])) $https = $_SERVER['REQUEST_SCHEME'] . '://';

        $data['host'] = $_SERVER['HTTP_HOST'];
        $data['path'] = $https . $data['host'];//depricated
        $data['site'] = $https . $data['host'];
        $data['conf'] = static::$conf;
        $data['email'] = $email;
        $data['time'] = time();
        $data['token'] = $user['user_id'] . '-' . $user['token'];

        $tpl = '-' . static::$name . '/i18n/' . $lang . '.mail.tpl';
        if (!Path::theme($tpl)) {
            $lang = static::$conf['lang']['def'];
            $tpl = '-' . static::$name . '/i18n/' . $lang . '.mail.tpl';
        }
        

        static::mailbefore($data);

        
        if (!empty($user['timezone'])) {
            $nowtimezone = date_default_timezone_get();
            @date_default_timezone_set($user['timezone']);
        }

        $subject = Template::parse($tpl, $data, $mailroot . '-subject');
        $body = Template::parse($tpl, $data, $mailroot);
        
        if (!empty($user['timezone'])) @date_default_timezone_set($nowtimezone);

        $r = Mail::html($subject, $body, true, $admin ? true : $email); //from to

        static::mailafter($data, $r);

        return $r;
    }
}
