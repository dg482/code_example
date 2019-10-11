<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;

class CalendarHolidays extends Model
{

    //
    protected $table = 'calendar_holiday';

    protected $fillable = ['name', 'alias', 'date_start', 'date_end', 'country_id',
        'data'];

    public static $calendar = [];

    protected $hidden = ['calendar'];

    /**
     * @param $month int
     */
    public static function loadEvent($month)
    {
        if (self::$calendar <> []) {
            return;
        }
        $items = self::where(function ($query) use ($month) {
            $query->whereRaw(
                \DB::raw('MONTH(date_start) = ' . $month)
            );
            $query->orWhere(function ($query) use ($month) {
                $query->whereRaw(\DB::raw('MONTH(date_end) = ' . $month));
            });
        })->get();
        foreach ($items as $item) {
            $date_end = strtotime($item->date_end);
            $date_start = strtotime($item->date_start);
            $day = (int)date('d', $date_start);
            /**
             * если месяц начала совпадает с текущим
             */
            if (date('m', $date_start) == $month) {
                if (!isset(self::$calendar[$day])) {
                    self::$calendar[$day] = [];
                }
                array_push(self::$calendar[$day], $item);
            }
            /**
             * Если есть дата окончания и она не совпадает с датой начала
             */
            if ($item->date_end && $item->date_start <> $item->date_end) {

                $end = (int)date('d', $date_end);
                $stop = ($end - $day);
                /**
                 * Если дата окончания в другом месяце
                 */
                if (date('m', $date_start) <> date('m', $date_end)) {
                    $end_day_month = (date('t', $date_start) - date('d', $date_start));
                    $new_day_month = date('d', $date_end);
                    $day = 0;
                    $stop = $new_day_month;
                }
                /**
                 * Если месяц совпадает с месяцем окончания
                 */
                if (date('m', $date_end) == $month) {
                    for ($i = 1; $i <= $stop; $i++) {
                        if (!isset(self::$calendar[$day + $i])) {
                            self::$calendar[$day + $i] = [];
                        }
                        array_push(self::$calendar[$day + $i], $item);
                    }
                }
            }
        }
    }

    /**
     * @param $day
     * @return null
     */
    public static function getItems($day)
    {
        return (isset(self::$calendar[$day])) ? self::$calendar[$day] : null;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }

        if ($this->data->{'name_' . \App::getLocale()}) {
            return $this->data->{'name_' . \App::getLocale()};
        }
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $result = [];
        if (\App::getLocale() <> 'ru') {
            array_push($result, \App::getLocale());
        }
        array_push($result, 'holiday');
        array_push($result, $this->alias);

        return url(implode('/', $result));
    }

    /**
     * @param string $lng
     * @return object
     */
    public function getLangText($lng = 'ru')
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
        $text = Content::getText($this->data->{'text_' . $lng});
        return $text;
    }

    /**
     * @return null
     */
    public function getPreview()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }

        return (isset($this->data->image)) ? $this->data->image : null;
    }

    /**
     * @return mixed
     */
    public function getYouTubeLink()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
        return $this->data->{'link_youtube_' . \App::getLocale()};
    }

    /**
     * @return mixed
     */
    public function getRelatedItems()
    {
        $th = $this;
        return self::where(function ($query) use ($th) {
            $query->where('date_start', '>', $th->date_start);
            $query->where('id', '!=', $th->id);
        })->orderBy('date_start')->limit(9)->get();
    }
}
