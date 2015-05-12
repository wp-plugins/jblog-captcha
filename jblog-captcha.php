<?php
/*
Plugin Name: JBlog Captcha
Version: 1.2.3
Description: Генерируемая капча из цифр и латинских символов. Предназначена только для использования в пользовательских формах и выводится простым шорткодом. Инструкции по использованию: Настройки — JBlog Captcha.
Plugin URI: http://jblog-project.ru/kapcha-v-polzovatelskix-formax/
Author: JBlog
Author URI: http://jblog-project.ru/
*/

/*  Copyright 2015  Roman Yakovlev  (email : admin@jblog-project.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * Регистрация хуков
 * */
register_activation_hook(__FILE__, array('JBlogCaptcha', 'activation'));
register_deactivation_hook(__FILE__, array('JBlogCaptcha', 'deactivation'));
register_uninstall_hook(__FILE__, array('JBlogCaptcha', 'uninstall'));
add_action('plugins_loaded', array('JBlogCaptcha', 'instance'));

if (!class_exists('JBlogCaptcha')) {
    class JBlogCaptcha
    {
        const JBC = 'JBlogCaptcha';
        public $plugin_name = '';
        public $plugin_dir = '';
        public $plugin_url = '';
        protected $data = array();

        protected static $_instance = null;

        /*
         * Singleton Pattern
         * */
        public static function instance()
        {
            if (self::$_instance == null) {
                self::$_instance = new JBlogCaptcha();
            }
            return self::$_instance;
        }

        /*
         * Constructor
         * */
        private function __construct()
        {
            /* установка параметров плагина */
            $this->plugin_name = plugin_basename(__FILE__);
            $this->plugin_dir = plugin_dir_path(__FILE__);
            $this->plugin_url = plugin_dir_url(__FILE__);
            /* подгружаем цвет шрифта */
            $this->data['color'] = get_option(self::JBC . '_color'); // array
            /* сообщения в админке */
            $this->data['messSettings'] = '';
            /* сообщение */
            $this->data['mess'] = '';
            /* верно ли введена капча? */
            $this->data['chek'] = get_option(self::JBC . '_is');

            if (function_exists('add_shortcode')) {
                add_shortcode('jbcptch', array(&$this, 'clientFormShortcode'));
            }

            if (is_admin())
                $this->admin_init();
        }

        private function __clone()
        {
        }

        /*
         * Access methods
         * */
        function getColorRed()
        {
            return $this->data['color']['red'];
        }

        function getColorGreen()
        {
            return $this->data['color']['green'];
        }

        function getColorBlue()
        {
            return $this->data['color']['blue'];
        }

        function getSysMess()
        {
            return $this->data['messSettings'];
        }

        function getChek()
        {
            return $this->data['chek']['is'];
        }

        function refresh()
        {
            $this->data['chek']['is'] = false;
            $this->data['chek']['sess'] = '';
            update_option(self::JBC . '_is', $this->data['chek']);
        }

        /* Get background CAPTCHA
         * -------------
         * Получить фон капчи, по умолчанию «Рябь»
         * */
        function getBg()
        {
            if (get_option(self::JBC . '_bg')) {
                $this->data['bg'] = get_option(self::JBC . '_bg');
            } else {
                $this->data['bg'] = 'bg';
            }
            return $this->data['bg'];
        }

        /* Get num char of CAPTCHA
         * -------------
         * Получить кол-во символов в капче, по умолчанию 5
         * */
        function getNum()
        {
            if (get_option(self::JBC . '_num')) {
                $this->data['num'] = get_option(self::JBC . '_num');
            } else {
                $this->data['num'] = 5;
            }
            return $this->data['num'];
        }

        /* Get registr
         * -------------
         * Получить опцию регистра, по умолчанию - регистрозависимый
         * */
        function getSens()
        {
            if (get_option(self::JBC . '_sens')) {
                $this->data['sens'] = get_option(self::JBC . '_sens');
            } else {
                $this->data['sens'] = 1;
            }
            return $this->data['sens'];
        }

        /*
         * Generate random string
         * */
        function getRndString($n)
        {
            $base = array('A','a','B','b','C','c','D','d','E','e','F','f','G','g','H','h','I','i','J','j','K','k','L','l','M','m','N','n','O','o','P','p','Q','q','R','r','S','s','T','t','U','u','V','v','W','w','X','x','Y','y','Z','z','1','2','3','4','5','6','7','8','9','0');
            return substr(md5(substr(md5(time() / sqrt(rand(1, 9999))), rand(0, 5), 10)), rand(0, 5), $n-3).$base[rand(0,20)].$base[rand(21,41)].$base[rand(41,61)];
        }

        /* compare value in this session and value user
        ------------------
         * Проверка капчи
         * Осуществляет проверку верно ли введена капча или нет
         *    Если да - обновляем директиву is в значение true
         *    Если нет - обновляем директиву is в значение false
         * */
        function chekSession()
        {
            ob_start();
            session_start();
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (!$_SESSION['str']) {
                    $this->data["mess"] = 'Включите поддержку картинок в браузере';
                } else {
                    $cpt = strip_tags(trim($_POST['str']));
                    if ($_SESSION['str'] == $cpt) {
                        $this->data["mess"] = 'Капча введена верно';
                        $this->data['chek']['is'] = true;
                        $this->data['chek']['sess'] = $cpt;
                        update_option(self::JBC . '_is', $this->data['chek']);
                    } else {
                        $this->data["mess"] = 'Капча введена неверно';
                        $this->data['chek']['is'] = false;
                        $this->data['chek']['sess'] = $cpt;
                        update_option(self::JBC . '_is', $this->data['chek']);
                    }
                }
            }
            $out = ob_get_clean();
            return $out;
        }

        /*
         *    echo do_shortcode("[jbcptch]") - shortcode - display captcha
         *
         * */
        function clientFormShortcode()
        {
            ob_start();
            ?>
            <div id="jbcptcha_div">
                <div id="generate-jbcptcha">
                    <?php /* Генератор картинки */ ?>
                    <?php $generate = $this->generateImage('captcha', $this->getSens());
                    if ($generate) {
                        ?>
                        <img src="<?php echo $this->plugin_url; ?>assets/img/<?php echo $generate; ?>.jpg">
                    <?php
                    }else{
                        echo "Произошла ошибка при загрузке изображения..";
                    }
                    ?>
                </div>
                <br>
                <div id="jbcptcha_settings">
                    <label>Введите символы</label>
                    <input id="jbcptcha_input" type="text" name="str" size="8" maxlength="5" autocomplete="off">
                    <input type="hidden" name="active_captcha" value="true"/>
                </div>
                <div id="jbcptcha_mess"><?php echo $this->data["mess"] ?></div>
            </div>
            <?php
            $out = ob_get_clean();
            return $out;
        }

        /*
         * Generate and save img
         * */
        function generateImage($string, $sens)
        {
            session_start();
            $c = $this->getNum();
            $bg = $this->getBg();
            $i = $this->LoadJpeg($this->plugin_url . 'assets/img/' . $bg . '.jpg');
            $color = $this->setColor($i, $this->getColorRed(), $this->getColorGreen(), $this->getColorBlue());
            imageantialias($i, true);
            $str = $this->getRndString($c);
            if($sens == 2)
                $str = strtolower($str);
            $_SESSION['str'] = $str;
            $x = 20;
            $y = 30;
            for ($j = 0; $j < $c; $j++) {
                $offsetX = rand(30, 40);
                $size = rand(20, 30);
                $angle = -25 + rand(0, 50);
                imagettftext($i, $size, $angle, $x, $y, $color, $this->plugin_dir . 'assets/fonts/bellb.ttf', $str[$j]);
                $x += $offsetX;
            }
            $path = $this->plugin_dir . 'assets/img/'.$string.'.jpg';
            $save = imagejpeg($i, $path, 50);
            $del = imagedestroy($i);
            if ($save && $del)
                return $string;
            return false;
        }

        /*
         * load img
         * */
        function LoadJpeg($imgname)
        {
            $im = @imagecreatefromjpeg($imgname);
            if (!$im) {
                $im = imagecreatetruecolor(210, 39);
                $bgc = imagecolorallocate($im, 255, 255, 255);
                $tc = imagecolorallocate($im, 0, 0, 0);

                imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

                imagestring($im, 1, 5, 5, 'Error load ' . $imgname, $tc);
            }
            return $im;
        }

        /*
         * set color fonts
         * */
        function setColor($im, $r, $g, $b)
        {
            if (!$r && !$g && !$b) {
                $r = 60;
                $g = 60;
                $b = 60;
            }
            $colorFonts = imagecolorallocate($im, $r, $g, $b);
            return $colorFonts;
        }

        /**
         * Admin panel
         */
        function admin_init()
        {
            add_action('admin_menu', array($this, 'admin_menu'));
        }

        function admin_menu()
        {
            add_options_page('Настройки JBlog Captcha', 'JBlog Captcha', 'manage_options', __FILE__, array($this, 'admin_options_page'));
        }

        function marker($flag = 'success')
        {
            if ($flag == 'error') {
                ?>
                <script type='text/javascript'>
                    window.onload = function () {
                        var mess = document.getElementById('jb_mess');
                        mess.className = 'error';
                    }
                </script>
            <?php
            } else {
                ?>
                <script type='text/javascript'>
                    window.onload = function () {
                        var mess = document.getElementById('jb_mess');
                        mess.className = 'updated';
                    }
                </script>
            <?php
            }
        }

        function admin_options_page()
        {
            if (!current_user_can('manage_options'))
                return;

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['jb_submit'])) {
                    if (isset($_POST['red']) && isset($_POST['green']) && isset($_POST['blue'])) {
                        if (!empty($_POST['red']) || $_POST['red'] >= 0 && !empty($_POST['green']) || $_POST['green'] >= 0 && !empty($_POST['blue']) || $_POST['blue'] >= 0) {
                            $red = (int)trim(strip_tags($_POST['red']));
                            $green = (int)trim(strip_tags($_POST['green']));
                            $blue = (int)trim(strip_tags($_POST['blue']));
                            if ($red >= 0 && $green >= 0 && $blue >= 0) {
                                if ($red > 255 || $green > 255 || $blue > 255) {
                                    $this->marker('error');
                                    $this->data['messSettings'] = 'Какой то из параметров превысил максимальное значение..';
                                } else {
                                    $this->data['messSettings'] = 'Настройки сохранены';
                                    $this->marker();
                                    $this->data['color']['red'] = $red;
                                    $this->data['color']['green'] = $green;
                                    $this->data['color']['blue'] = $blue;
                                    update_option(self::JBC . '_color', $this->data['color']);
                                }
                            } else {
                                $this->marker('error');
                                $this->data['messSettings'] = 'Какой то из параметров содержит недопустимые значения..';
                            }
                        } else {
                            $this->marker('error');
                            $this->data['messSettings'] = 'Вы указали не все параметры..';
                        }
                    }
                    if (isset($_POST['getbg']) && "" != $_POST['getbg']) {
                        $this->data['bg'] = $_POST['getbg'];
                        update_option(self::JBC . '_bg', $this->data['bg']);
                    }
                    if (isset($_POST['getnum']) && "" != $_POST['getnum']) {
                        $this->data['num'] = $_POST['getnum'];
                        update_option(self::JBC . '_num', $this->data['num']);
                    }
                    if (isset($_POST['getsens']) && "" != $_POST['getsens']) {
                        $this->data['sens'] = $_POST['getsens'];
                        update_option(self::JBC . '_sens', $this->data['sens']);
                    }
                }
            }
            include $this->plugin_dir . 'include/admin.php';
        }

        /*
         * On activation
         * */
        function activation()
        {
            // при активации
            if (!current_user_can('activate_plugins'))
                return;
        }

        /*
         * On deactivation
         * */
        function deactivation()
        {
            // при деактивации
            if (!current_user_can('activate_plugins'))
                return;
            $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
            check_admin_referer("deactivate-plugin_{$plugin}");

            delete_option(self::JBC);
            delete_option(self::JBC . '_color');
            delete_option(self::JBC . '_bg');
            delete_option(self::JBC . '_is');
            delete_option(self::JBC . '_num');
            delete_option(self::JBC . '_sens');
        }

        /*
         * On uninstall
         * */
        function uninstall()
        {
            // при удалении
            if (!current_user_can('activate_plugins'))
                return;
            check_admin_referer('bulk-plugins');
            // Важно: проверим тот ли это файл, который
            // был зарегистрирован во время удаления плагина.
            if (__FILE__ != WP_UNINSTALL_PLUGIN)
                return;
            // проверка пройдена успешно. Начиная от сюда удаляем опции и все остальное.
            delete_option(self::JBC);
            delete_option(self::JBC . '_color');
            delete_option(self::JBC . '_bg');
            delete_option(self::JBC . '_is');
            delete_option(self::JBC . '_num');
            delete_option(self::JBC . '_sens');
        }
    }
}