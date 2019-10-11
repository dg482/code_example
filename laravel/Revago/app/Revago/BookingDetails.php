<?php namespace App\Revago;

use Illuminate\Database\Eloquent\Model;

use App\Core\SystemEmail,
    App\Core\SystemSetting,
    App\Core\CurrencyConverter;

class BookingDetails extends Model
{
    const TYPE_LOCAL = 'local';

    protected $table = 'booking_details';

    protected $fillable = ['item_id', 'email', 'fio', 'order_key', 'data', 'type'];

    /**
     * Завершение бронирование, отпаравка почты
     *
     * @param $data
     */
    public static function completeOrder($data)
    {
        $locale = \App::getLocale();
        $set_currency = \App\User::getCurrency();
        $data['code'] = $data['item']->code;
        $data['irent_booking_id'] = $data['external_id'];
        $data['link'] = $data['item']->getUrl();
        $data['item_name'] = $data['item']->getTitle();
        $data['item_address'] = $data['item']->getAddress();
        $data['total'] = CurrencyConverter::_(($data['price'] * $data['days']), $set_currency);

        BookingDetails::create([
            'item_id' => $data['item']->id,
            'email' => $data['email'],
            'fio' => $data['name'] . ' ' . $data['surname'],
            'order_key' => $data['external_id'],
            'type' => $data['type'],
            'data' => json_encode($data)
        ]);

        $userEmail = SystemEmail::where('alias', 'booking_irent_user')->first();
        $userEmail->data = json_decode($userEmail->data);
        if (isset($userEmail->data->{'text_' . $locale})) {
            $userEmail->text = $userEmail->data->{'text_' . $locale};
        }
        unset($data['item']);
        $userEmail->text = SystemEmail::getText($userEmail->text, $data);

        /**
         * Отправка почты пользователю
         */
        \Mail::send('emails.system', [
            'content' => $userEmail->text
        ], function ($message) use ($data) {
            $message->from('admin@revago.com', 'Автоматическое уведомление');

            $message->to($data['email'], $data['email']);
            $message->subject($data['name'] . ', вы забронировали жильё на Revago');
        });

        $adminSetting = SystemSetting::where('name', 'Список eamil для отправки информации по бронированию I Rent')->first();
        $adminEmail = SystemEmail::where('alias', 'booking_irent_admin')->first();

        $adminEmail->text = SystemEmail::getText($adminEmail->text, $data);

        /**
         * Отправка почты администратору
         */
        \Mail::send('emails.system', [
            'content' => $adminEmail->text
        ], function ($message) use ($adminSetting) {
            $message->from('admin@revago.com', 'Автоматическое уведомление');
            $emails = explode(',', $adminSetting->value);
            if (count($emails) > 1) {
                $message->to($emails[0], $emails[0]);
                unset($emails[0]);
                foreach ($emails as $email) {
                    $message->cc($email, $email);
                }
            } else {
                $message->to($emails[0], $emails[0]);
            }
            $message->subject('Новая бронь на Revago');
        });
    }
}
