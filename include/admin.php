<style type="text/css">
    #tbl_setting table {
        float: left;
        margin-bottom: 15px;
    }

    #color_generation {
        overflow: hidden;
        padding: 10px;
        width: 30%;
        background: #f9f9f9;
        border: 1px solid #ccc;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
        float: left;
    }

    #demo {
        width: 210px;
        height: 174px;
        margin-left: 1%;
        float: left;
        background: #f9f9f9;
        border: 1px solid #ccc;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        border-radius: 3px;
    }

    #color_generation tr > td:first-child {
        padding-right: 10px;
    }

</style>

<div class='wrap'>
    <h2>Настройки JBlog Captcha</h2>
    <?php
    $obj_admin = JBlogCaptcha::instance();
    echo "<div id='jb_mess'>" . $obj_admin->getSysMess() . "</div>";
    ?>
    <h3>Общие »</h3>

    <p>Укажите RGB цвет для шрифта символов капчи (по умолчанию R = 60, G = 60, B = 60)</p>

    <p>Задайте фон (по умолчанию «рябь»)</p>

    <p>Задайте количество символов в капче (по умолчанию 5)</p>

    <form id="color_generation" action="" method="post">
        <div id="tbl_setting">
            <table style="border-right: 1px dotted #ccc;
                          padding-right: 20px;">
                <tr>
                    <td><label for="red">R:</label></td>
                    <td><input name="red" type="text" size="3" maxlength="3"
                               value="<?php echo $obj_admin->getColorRed(); ?>"/></td>
                </tr>
                <tr>
                    <td><label for="green">G:</label></td>
                    <td><input name="green" type="text" size="3" maxlength="3"
                               value="<?php echo $obj_admin->getColorGreen(); ?>"/></td>
                </tr>
                <tr>
                    <td><label for="blue">B:</label></td>
                    <td><input name="blue" type="text" size="3" maxlength="3"
                               value="<?php echo $obj_admin->getColorBlue(); ?>"/></td>
                </tr>
            </table>
            <table style="border-right: 1px dotted #ccc;
                          padding-right: 20px;">
                <tr>
                    <td><input type="radio" name="getbg" id="getbg" value="bg" <?php if ($obj_admin->getBg() == 'bg') {
                            echo "checked";
                        } ?>></td>
                    <td>Рябь</td>
                </tr>
                <tr>
                    <td><input type="radio" name="getbg" id="getbg1"
                               value="bg1" <?php if ($obj_admin->getBg() == 'bg1') {
                            echo "checked";
                        } ?>></td>
                    <td>Круги</td>
                </tr>
                <tr>
                    <td><input type="radio" name="getbg" id="getbg2"
                               value="bg2" <?php if ($obj_admin->getBg() == 'bg2') {
                            echo "checked";
                        } ?>></td>
                    <td>Вода</td>
                </tr>
                <tr>
                    <td><input type="radio" name="getbg" id="getbg3"
                               value="bg3" <?php if ($obj_admin->getBg() == 'bg3') {
                            echo "checked";
                        } ?>></td>
                    <td>Свечение</td>
                </tr>
                <tr>
                    <td><input type="radio" name="getbg" id="getbg4"
                               value="bg4" <?php if ($obj_admin->getBg() == 'bg4') {
                            echo "checked";
                        } ?>></td>
                    <td>Узоры</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td>
                        <input type="radio" name="getnum" id="getnum"
                               value="5" <?php if ($obj_admin->getNum() == 5) {
                            echo "checked";
                        } ?>>
                    </td>
                    <td><strong>5</strong> символов</td>
                </tr>
                <tr>
                    <td>
                        <input type="radio" name="getnum" id="getnum1"
                               value="4" <?php if ($obj_admin->getNum() == 4) {
                            echo "checked";
                        } ?>>
                    </td>
                    <td><strong>4</strong> символа</td>
                </tr>
                <tr>
                    <td>
                        <input type="radio" name="getnum" id="getnum2"
                               value="3" <?php if ($obj_admin->getNum() == 3) {
                            echo "checked";
                        } ?>>
                    </td>
                    <td><strong>3</strong> символа</td>
                </tr>
            </table>
        </div>
        <div style="clear: both;"></div>
        <input type="submit" class="button-primary" name="jb_submit" value="Сохранить"/>
    </form>
    <div id="demo">
        <?php $generate = $obj_admin->generateImage('captcha');
        if ($generate) {
            ?>
            <img style="padding: 68px 0;" src="<?php echo $obj_admin->plugin_url; ?>assets/img/<?php echo $generate; ?>.jpg">
        <?php
        }else{
            echo "Произошла ошибка при загрузке изображения..";
        }
        ?>
    </div>
    <div style="clear: both;"></div>
    <div>
        <h3>Установка</h3>
        <ul>
            <ol> —1. Скачайте и установите плагин</ol>
            <ol> —2. Активируйте его</ol>
            <ol> —3. Перейдите в <strong>Настройки</strong> » <strong>JBlog Captcha</strong></ol>
            <ol> —4. Поменяйте настройки по умолчанию, сохраните и наслаждайтесь эффектом!</ol>
        </ul>
    </div>
    <div>
        <h3>Инструкции</h3>
        <ul>
            <ol> —1. Для вызова html-формы капчи, используйте <strong>шорткод</strong>:
                <code>do_shortcode("[jbcptch]")</code>, или с проверкой <code>if(class_exists('JBlogCaptcha')){print
                    do_shortcode("[jbcptch]");}</code>, возвращающий код сгенерированной картинки и поле ввода. <em>*
                    Второй вариант вызова предпочтительнее.</em></ol>
        </ul>
    </div>
    <div>
        <h3>Применение</h3>
        <h4>Посылка через AJAX »</h4>
        <ul>
            <ol> —1. При передачи данных методом POST через AJAX, возьмите значение капчи (например, <code>var str =
                    $('#jbcptcha_input').val()</code>, если используете jQuery) и поместите её в переменную
                «<strong>str</strong>» !! (<code>data:{"str": str}</code>), таким образом устанавливая <code>$_POST["str"]</code>
                в файле-обработчике. Данная POST переменная необходима для дальнейшей проверки!
            </ol>
            <ol> —2. В файле-обработчике: <strong>перед условием</strong> ( <code> ... if(){} </code> ), в котором
                проверяете данные, используйте инструкцию: <code>JBlogCaptcha::instance()->chekSession();</code>,
                которая проверяет верно ли введена капча.
            </ol>
            <ol> —3. В файле-обработчике: <strong>в условии</strong> (<code>if( var_1 && var_2 && ... ){}</code>),
                используйте инструкцию: <code>JBlogCaptcha::instance()->getChek()</code>, которая вернет истину, если
                капча введена верно, наряду с проверкой собственных данных.
            </ol>
            <ol> —4. В файле-обработчике: <strong>в теле условия</strong>, при котором возвращается истина, ( <code>if(){...}</code>
                ), в самом конце используйте инструкцию: <code>JBlogCaptcha::instance()->refresh();</code>, которая
                сбрасывает капчу в первоначальное состояние.
            </ol>
        </ul>
        <h4>Посылка из формы »</h4>
        <ul>
            <ol> —1. При посылке из формы, установить тегу <code>form</code> атрибут <code>method="post"</code>.</ol>
            <ol> —2. Вызвать капчу в том месте где необходимо (см. <strong>Инструкции</strong>).</ol>
            <ol> —3. Проделать п. 2, 3, 4 из «Посылка через AJAX»</ol>
        </ul>
    </div>
</div>
