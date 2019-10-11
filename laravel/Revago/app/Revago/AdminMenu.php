<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;

use App\User as User;

class AdminMenu extends Model
{

    protected static $menu = [
        User::USER_TYPE_ADMIN => [
            'Публичная часть' => [
                '/admin/public/menu' => 'Меню навигации',
                //'/admin/public/pages' => 'Страницы',
                '/admin/public/include-area' => 'Включаемые области',
                '/admin/public/content' => 'Контент',
                '/admin/public/content-category' => 'Контент-Категории',
                '/admin/public/calendar-holiday' => 'Календарь праздников',
                'divider',
                '/admin/public/modules' => 'Модули',
            ],
            'Каталог' => [
                '/admin/catalog/country' => 'Регион\\Город',
                '/admin/catalog/category' => 'Категории',
                '/admin/catalog/items' => 'Позиции',
                '/admin/catalog/booking-items' => 'Бронирование позиции',
                'divider',
                '/admin/catalog/review' => 'Отзывы',
            ],
            'Параметры' => [
                '/admin/system/email' => 'Email шаблоны',
                '/admin/system/setting' => 'Настройки скрипта',
            ],
//            'Пользователи' => [
//                '/admin/users/' => 'Список пользователей',
//                'divider',
//            ]
        ]
    ];

    public function __construct()
    {

    }

    /**
     * @param string $active
     * @return string
     */
    public static function get($active = '')
    {
        $str = '';
        if (\Auth::check()) {
            $user = \Auth::user();
            $user_type = $user->type;
            if (isset(self::$menu[$user_type])) {
                //self::$menu[$user_type]['Профиль']['dashboard'] = $user->email;
                foreach (self::$menu[$user_type] as $name => $blocks) {
                    if (is_array($blocks)) {
                        $str .= <<<HTML
<li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{$name} <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
HTML;

                        foreach ($blocks as $nam => $block) {
                            $class = ($active == $nam) ? 'class="active"' : '';
                            if ($block == 'divider') {
                                $str .= '<li class="divider"></li>';
                            } else {
                                $str .= '<li ' . $class . '>' . \HTML::link($nam, $block) . '</li>';
                            }
                        }
                        $str .= <<<HTML
   </ul>
</li>
HTML;

                    } else {
                        $class = ($active == $blocks) ? 'class="active"' : '';
                        if ($blocks == 'divider') {
                            $str .= '<li class="divider"></li>';
                        } else {
                            $str .= '<li ' . $class . '>' . HTML::link($name, $blocks) . '</li>';
                        }
                    }

//                    if ($name == 'divider') {
//                        $str .= '<li class="divider"></li>';
//                    } else {
//                        $str .= '<li ' . $class . '>' . HTML::link($route, $name) . '</li>';
//                    }
                }
            }
        }

        return $str;
    }


}
