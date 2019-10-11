<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model,
    Illuminate\Database\Eloquent\SoftDeletes;

class CatalogCountryRegion extends Model
{

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public $table = 'catalog_country_region';

    protected $fillable = ['name_ru', 'name_en', 'name_es', 'parent_id', 'data', 'alias'];

    /**
     * @return mixed
     */
    public function getName()
    {
        if ($this->{'name_' . \App::getLocale()}) {
            return $this->{'name_' . \App::getLocale()};
        }
        return $this->name_ru;
    }

    /**
     * @param bool|false $all
     * @return array|mixed|null
     */
    public function getMetaTitle($all = false)
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
        if ($all) {
            return [
                'meta_title_ru' => $this->data->meta_title_ru,
                'meta_title_en' => $this->data->meta_title_en,
                'meta_title_es' => $this->data->meta_title_es,
            ];
        }
        if ($this->data->{'meta_title_' . \App::getLocale()}) {
            return $this->{'meta_title_' . \App::getLocale()};
        }
        return (isset($this->data->meta_title_ru) && $this->data->meta_title_ru <> '') ? $this->data->meta_title_ru : null;
    }

    public function getData()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
    }

    /**
     * Получает описание в текущем языке
     *
     * @return object
     */
    public function getDescription()
    {
        if (is_string($this->data)) {
            $this->data = json_decode($this->data);
        }
        $data = [];

        $description = $this->data->{'description_' . \App::getLocale()};


        $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
        $tagPos = preg_match($pattern, $description);

        if ($tagPos == 0) {
            $data['introtext'] = $description;
            $data['fulltext'] = '';
        } else {
            list ($data['introtext'], $data['fulltext']) = preg_split($pattern, $description, 2);
        }
        return (object)$data;
    }

    /**
     * @param int $limit
     * @return mixed
     */
    public function getCatalogItems($limit = 100)
    {
        $items = CatalogItems::where('region_id', $this->id)->paginate($limit);
        return $items;
    }
}
